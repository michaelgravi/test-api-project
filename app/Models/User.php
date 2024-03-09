<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
        'last_seen'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
    ];

    /**
     * Get the transactions associated with this user.
     *
     * @return \Illuminate\Database\Eloquent\Collection|array
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'author_id');
    }

    /**
     * Get transactions list.
     *
     */
    public function getTransactionsList($filter)
    {
        $tranasctions = $this->transactions();
        return $this->sortTransactionsByFilter(explode(",", $filter), $tranasctions);
    }

    /**
     * Filter transaction.
     *
     */
    public function sortTransactionsByFilter($filter, $tranasctions)
    {
        $tranasctions = in_array("amount", $filter) ? $this->sortTransactionsByAmount($tranasctions) : $tranasctions;
        $tranasctions = in_array("add_time", $filter) ? $this->sortTransactionsByAddTime($tranasctions) : $tranasctions;
        $tranasctions = in_array("incoming", $filter) ? $this->sortTransactionsByIncoming($tranasctions) : $tranasctions;
        $tranasctions = in_array("debiting", $filter) ? $this->sortTransactionsByDebiting($tranasctions) : $tranasctions;
        return $tranasctions->get();
    }

    /**
     * Sort transaction by amount.
     *
     */
    public function sortTransactionsByAmount($tranasctions)
    {
        return $tranasctions->orderByDesc('amount');
    }
    /**
     * Sort transaction by add time.
     *
     */
    public function sortTransactionsByAddTime($tranasctions)
    {
        return $tranasctions->orderByDesc('created_at');
    }

    /**
     * Sort transaction by incoming.
     *
     */
    public function sortTransactionsByIncoming($tranasctions)
    {
        return $tranasctions->where('amount', '>=', 0);
    }

    /**
     * Sort transaction by debiting.
     *
     */
    public function sortTransactionsByDebiting($tranasctions)
    {
        return $tranasctions->where('amount', '<', 0);
    }

    /**
     * Get transaction amount.
     *
     */
    public function getTransactionsAmount($options)
    {
        $tranasctions = $this->getTransactionsForTheSelectedPeriod($options, $this->transactions());
        $tranasctions = $this->sortTransactionsByFilter(array($options->get('filter')), $tranasctions);
        return $this->calculateTheAmount($tranasctions);
    }

    /**
     * Sort transaction by selected period.
     *
     */
    public function getTransactionsForTheSelectedPeriod($options, $tranasctions)
    {
        $tranasctions = $tranasctions->where('created_at', '>=', $options->get('start_date'));
        $tranasctions = $tranasctions->where('created_at', '<=', $options->get('end_date'));
        return $tranasctions;
    }

    /**
     * Calculate amount from given transactions.
     *
     */
    public function calculateTheAmount($tranasctions)
    {
        $amount = 0;
        foreach ($tranasctions as $tranasction) {
            $amount += $tranasction->amount_decimal;
        }
        return number_format($amount, 2);
    }

    /**
     * Get user USD balance.
     *
     */
    public function USDBalance()
    {
        $tranasctions = $this->transactions;
        $amount = number_format($this->calculateTheAmount($tranasctions), 2);
        $USD_balance = $this->USDConvert($amount);
        return number_format($USD_balance, 2);
    }

    /**
     * Convert user balance in USD.
     *
     */
    public function USDConvert($amount)
    {
        $amount = exchangeRate()->USDConvert($amount);
        return number_format($amount, 2);
    }
}

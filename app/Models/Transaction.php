<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $appends = array('amount_decimal');
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'author_id',
        'amount',
        'amount_decimal'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'amount',
    ];

    public function getAmountDecimalAttribute()
    {
        return number_format($this->amount / 100, 2);
    }

    /**
     * Get the user associated with this transaction.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function author()
    {
        return $this->hasOne(User::class, 'id', 'author_id');
    }

    /**
     * Delete a transaction.
     *
     * @param int $id
     * @return array
     */
    public function deleteTransaction(int $id): array
    {
        try {
            $transaction = $this->where('author_id', auth()->user()->id)->find($id);
            $transaction->delete();
            return ['message' => __('transaction_message.delete')];
        } catch (ModelNotFoundException $e) {
            return ['error' => __('transaction_message.not_found')];
        }
    }

    /**
     * Sort transaction by amount.
     *
     */
    public function sortByAmount()
    {
        return $this->sortBy('amount');
    }
}

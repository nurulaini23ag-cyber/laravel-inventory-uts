<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'user_id',
        'purchase_number',
        'total',
        'status',
        'purchased_at',
        'notes',
    ];

    protected $casts = [
        'total'        => 'decimal:2',
        'purchased_at' => 'datetime',
    ];

    /**
     *  Purchase belongsTo Supplier
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     *  Purchase belongsTo User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     *  Purchase hasMany PurchaseItems
     */
    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }
}

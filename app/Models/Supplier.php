<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_info',
        'phone',
        'email',
    ];

    /**
     *  Supplier hasMany Purchases
     */
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    /**
     * Many-to-Many Products
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_suppliers')
            ->withPivot('supplier_price')
            ->withTimestamps();
    }
}

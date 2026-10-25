<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'sku',
        'name',
        'purchase_price',
        'selling_price',
        'stock',
        'minimum_stock',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'selling_price'  => 'decimal:2',
        'stock'          => 'integer',
        'minimum_stock'  => 'integer',
    ];

    /**
     * Backward-compatible accessor for price
     */
    public function getPriceAttribute()
    {
        return $this->selling_price;
    }

    /**
     *  Product belongsTo Category
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     *  Product hasMany PurchaseItems
     */
    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    /**
     *  Product hasMany SaleItems
     */
    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    /**
     * Many-to-Many Supplier
     */
    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'product_suppliers')
            ->withPivot('supplier_price')
            ->withTimestamps();
    }

    /**
     * Audit Stock Movements
     */
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }
}

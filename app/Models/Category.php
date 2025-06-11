/**
 * Category Model
 *
 * This model represents a product category in the e-commerce system.
 * Categories can have a hierarchical structure with parent-child relationships.
 *
 * @package App\Models
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Category
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property int|null $parent_id
 * @property string|null $description
 * @property string|null $image_url
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'is_active',
        'order',
    ];

    /**
<<<<<<<<< Temporary merge branch 1
     * Get the parent category of this category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get the child categories of this category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Get the products associated with this category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
=========
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get the products for the category.
>>>>>>>>> Temporary merge branch 2
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
<<<<<<<<< Temporary merge branch 1
     * Get the discounts associated with this category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function discounts(): HasMany
=========
     * Get the parent category.
     */
    public function parent()
>>>>>>>>> Temporary merge branch 2
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**

     * Get the child categories.
     */

     * Get the child categories of this category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**

     * Get the discounts that apply to this category.
     */
    public function discounts(): BelongsToMany
     * Get the products associated with this category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products(): HasMany

    {
        return $this->belongsToMany(Discount::class);
    }

    /**
     * Scope a query to only include active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**

     * Scope a query to only include root categories.
     */
    public function scopeRoot($query)
     * Get the discounts associated with this category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function discounts(): HasMany

    {
        return $query->whereNull('parent_id');
    }
} 

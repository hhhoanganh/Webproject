<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Product extends Model
{
   protected $table = 'products';

   protected $fillable = [
       'name',
       'description',
       'thumpnail',
       'image_id',
       'price',
       'created_at',
       'created_by',
       'updated_at',
       'updated_by',
   ];
}

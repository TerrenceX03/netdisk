<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Author extends Model{
	protected $fillable = [
		'name', 'email', 'github', 'location', 'latest_article_published'];

	protected $hidden = [];
}
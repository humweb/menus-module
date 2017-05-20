<?php

use Humweb\Menus\Models\MenuItem;
use Humweb\Menus\Models\Menu;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenusTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('menus', function ($table)
		{
			$table->increments('id');
			$table->string('title');
			$table->string('slug');
		});

		Schema::create('menu_items', function ($table)
		{
			$table->increments('id');
			$table->string('title');
			$table->string('url')->nullable();
			$table->integer('parent_id')->default(0);
			$table->integer('menu_id');
			$table->integer('order')->nullable();
			$table->text('permissions')->nullable();
			$table->text('content')->nullable();
			$table->index('parent_id');
			$table->index('menu_id');
		});

		$menu = Menu::create([
			'title' => 'Main',
			'slug' => 'main'
		]);

		$menuItem = MenuItem::create([
			'title' => 'Home',
			'url' => '/',
			'menu_id' => $menu->id
		]);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('menu_items');
		Schema::drop('menus');
	}

}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEarlyAccessTable extends Migration
{
    /**
     * @var string
     */
    private $table;

    /**
     * CreateEarlyAccessTable constructor.
     */
    public function __construct()
    {
        $this->table = config('early-access.services.database.table_name');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('email');
            $table->dateTime('subscribed_at')->nullable();
            $table->dateTime('verified_at')->nullable();
            $table->softDeletes();

            $table->unique(['email', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->table);
    }
}

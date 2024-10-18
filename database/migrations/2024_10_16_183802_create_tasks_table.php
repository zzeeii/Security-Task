<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->enum('type', ['Bug', 'Feature', 'Improvement']);
            $table->enum('status', ['Open', 'In Progress', 'Completed', 'Blocked']);
            $table->enum('priority', ['Low', 'Medium', 'High']);
            $table->date('due_date');
            $table->foreignId('assigned_to')->constrained('users'); 
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}

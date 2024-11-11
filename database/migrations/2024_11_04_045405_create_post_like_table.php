<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('post_like', function (Blueprint $table) {
            $table->id();
            // $table->integer('likes')->default(0);
            $table->boolean('upvote_status')->default(false);
            $table->foreignIdFor(Post::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_like');
    }
};


/*
    if record not found:
        create new record and insert true or false ofr upvote_status

    if record found:
        if existing record is true and so is the request, delete the record
        
        if existing value and new vlaues are different, change the existing value to opposite of it 



    total likes: total records of the post and calculate the likes 

*/
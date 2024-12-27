<?php

use App\Models\Comment;
use App\Models\Post;
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
        Schema::create('notifications', function (Blueprint $table) {

            $table->id();
            $table->enum('notification_type', ['post', 'comment', 'new_comment', 'others']);

            // $table->json('data');
            $table->string('title');
            $table->string('message');

            $table->unsignedBigInteger('interactor_user_id');
            $table->foreignIdFor(model: Post::class);
            $table->foreignIdFor(Comment::class)->nullable()->constrained()->onDelete('cascade');

            $table->unsignedBigInteger('notifiable_id');
            $table->boolean(column: 'is_seen')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->foreign('notifiable_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('interactor_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};

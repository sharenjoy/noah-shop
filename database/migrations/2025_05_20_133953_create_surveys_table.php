<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Sharenjoy\NoahCms\Models\Survey\Survey;
use Spatie\Translatable\HasTranslations;

class CreateSurveysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (in_array(HasTranslations::class, class_uses(Survey::class))) {
            $fieldDataType = [
                'title' => 'json',
                'description' => 'json',
                'content' => 'json',
            ];
        } else {
            $fieldDataType = [
                'title' => 'string',
                'description' => 'text',
                'content' => 'text',
            ];
        }

        Schema::create('srv_surveys', function (Blueprint $table) use ($fieldDataType) {
            $table->id();
            $table->{$fieldDataType['title']}('title');
            $table->{$fieldDataType['description']}('description')->nullable();
            $table->{$fieldDataType['content']}('content')->nullable();
            $table->integer('img')->nullable();
            $table->text('album')->nullable();
            $table->string('slug', 100);
            $table->boolean('allow_guest')->default(false);
            $table->boolean('limit')->default(false);
            $table->integer('limit_amount')->nullable();
            $table->integer('limit_per_participant')->nullable();
            $table->boolean('purchaseable')->default(false);
            $table->boolean('purchase_depends_on_option')->default(false);
            $table->integer('purchase_price')->nullable();
            $table->boolean('forever')->default(false);
            $table->boolean('is_active')->default(false);
            $table->timestamp('started_at')->nullable();
            $table->datetime('expired_at')->nullable();
            $table->datetime('display_expired_at')->nullable();
            $table->timestamp('published_at');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('srv_surveys');
    }
}

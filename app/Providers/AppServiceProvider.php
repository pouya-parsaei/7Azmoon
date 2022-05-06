<?php

namespace App\Providers;

use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\QuestionRepositoryInterface;
use App\Repositories\Contracts\QuizRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\EloquentCategoryRepository;
use App\Repositories\Eloquent\EloquentQuestionRepository;
use App\Repositories\Eloquent\EloquentQuizRepository;
use App\Repositories\Eloquent\EloquentUserRepository;
use App\Repositories\Json\JsonUserRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    public function boot()
    {
        $this->app->bind(UserRepositoryInterface::class,EloquentUserRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class,EloquentCategoryRepository::class);
        $this->app->bind(QuizRepositoryInterface::class,EloquentQuizRepository::class);
        $this->app->bind(QuestionRepositoryInterface::class,EloquentQuestionRepository::class);
    }
}

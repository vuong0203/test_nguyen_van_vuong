<?php

use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\AddressController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DetailController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\MailController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\ReplyController;
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\CuratorController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

//ログイン
Route::middleware(['guest:admin', 'throttle:5'])->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store']);
    //---------------------OAuth-----------------------------------------------
    Route::prefix('login/{provider}')->where(['provider' => '(line|twitter|facebook|google|yahoo)'])->group(function () {
        Route::get('/', [LoginController::class, 'redirectToProvider'])->name('sns_login.redirect');
        Route::get('/callback', [LoginController::class, 'handleProviderCallback'])->name('sns_login.callback');
    });
});

//管理画面のルート
Route::middleware('auth:admin')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    //ユーザー管理
    Route::resource('user', UserController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::get('user/password_reset/{user}', [UserController::class, 'passwordReset'])->name('user.password_reset');
    Route::prefix('user/{user}')->group(function () {
        Route::get('address/edit', [AddressController::class, 'edit'])->name('address.edit');
        Route::resource('address', AddressController::class, ['only' => ['create', 'store', 'update']]);
        Route::get('profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::resource('profile', ProfileController::class, ['only' => ['create', 'store', 'update']]);
        Route::get('download_identify_image', [UserController::class, 'downloadIdentifyImage'])->name('user.download_identify_image');
    });

    //プロフィール管理
    Route::resource('detail', DetailController::class, ['only' => ['show', 'edit', 'update']]);

    //プロジェクト管理
    Route::resource('project', ProjectController::class, ['only' => ['index', 'create', 'store', 'edit', 'update', 'destroy', 'show']]);
    Route::post('project/operate_projects', [ProjectController::class, 'operate_projects'])->name('project.operate_projects');
    Route::delete('project/file/{project_file}', [ProjectController::class, 'deleteFile'])->name('project.delete.file');
    Route::post('project/upload_editor_file', [ProjectController::class, 'uploadEditorFile'])->name('upload_editor_file');
    // Route::patch('project/{project}/increment_likes', [ProjectController::class, 'incrementLikes'])->name('project.increment_likes');
    // Route::patch('project/{project}/decrement_likes', [ProjectController::class, 'decrementLikes'])->name('project.decrement_likes');
    Route::prefix('project/{project}')->group(function () {
        Route::resource('plan', PlanController::class, ['only' => ['create', 'store', 'edit', 'update']]);
        Route::get('plan/{plan}/preview', [PlanController::class, 'preview'])->name('plan.preview');
        Route::get('send_back', [ProjectController::class, 'sendBack'])->name('project.send_back');
        Route::get('approved', [ProjectController::class, 'approved'])->name('project.approved');
        Route::get('under_suspension', [ProjectController::class, 'underSuspension'])->name('project.under_suspension');
        Route::get('release', [ProjectController::class, 'release'])->name('project.release');
        Route::get('output_purchases_list_to_csv', [ProjectController::class, 'outputPurchasesListToCsv'])->name('project.output_purchases_list_to_csv');
        Route::put('associate_curator', [ProjectController::class, 'associateCurator'])->name('project.associate_curator');
        Route::put('associate_option_fee', [ProjectController::class, 'associateOptionFee'])->name('project.associate_option_fee');
        Route::post('remittance', [ProjectController::class, 'remittance'])->name('project.remittance');
        Route::post('again_remittance', [ProjectController::class, 'againRemittance'])->name('project.again_remittance');
        Route::resource('report', ReportController::class, ['only' => ['create', 'store', 'edit', 'update', 'destroy']]);
    });
    Route::get('project/{project}/create_cheering_users_mail', [MailController::class, 'createCheeringUsersMail'])->name('project.mail.create_cheering_users_mail');
    Route::post('project/preview_cheering_users_mail', [MailController::class, 'previewCheeringUsersMail'])->name('project.mail.preview_cheering_users_mail');
    Route::post('project/send_cheering_users_mail', [MailController::class, 'sendCheeringUsersMail'])->name('project.mail.send_cheering_users_mail');
    Route::delete('plan/image/{plan}', [PlanController::class, 'deleteImage'])->name('plan_image.destroy');
    // NOTE:現状オプションは使用しない為、コメントアウト
    // Route::delete('plan/option/{option}', [PlanController::class, 'deleteOption'])->name('option.destroy');
    Route::delete('report/image/{report_image}', [ReportController::class, 'deleteImage'])->name('report.image');

    // リターン管理
    Route::resource('plan', PlanController::class, ['only' => ['index', 'show', 'destroy']]);

    // 応募者管理
    Route::resource('payment', PaymentController::class, ['only' => ['index', 'show', 'destroy']]);
    Route::post('payment/alter_sales', [PaymentController::class, 'alterSales'])->name('payment.alter_sales');
    Route::post('payment/alter_cancel', [PaymentController::class, 'alterCancel'])->name('payment.alter_cancel');

    // 活動報告管理
    Route::resource('report', ReportController::class, ['only' => ['index', 'show']]);

    // コメント管理
    Route::resource('comment', CommentController::class, ['only' => ['index', 'show', 'destroy']]);

    //返信管理
    Route::post('reply/{comment}', [ReplyController::class, 'store'])->name('reply.store');
    Route::resource('reply', ReplyController::class, ['only' => ['update', 'destroy']]);

    //タグ管理
    Route::resource('tag', TagController::class, ['only' => ['index', 'create', 'store', 'edit', 'update', 'destroy']]);

    // キュレーター管理
    Route::resource('curator', CuratorController::class, ['only' => ['index', 'create', 'store', 'edit', 'update', 'destroy']]);

    // メッセージ管理
    Route::get('message_index/{selected_message?}', [MessageController::class, 'index'])->name('message.index');
    Route::get('message/{user}', [MessageController::class, 'show'])->name('message.show');
    Route::post('message/{user}', [MessageController::class, 'store'])->name('message_content.store');
    Route::get('message/{message_content}/file_download', [MessageController::class, 'fileDownload'])->name('message_content.file_download');
});

// 上記以外のパラメーターを取得して、route('admin.dashboard')にリダイレクトする。
Route::get('/{any?}', function () {
    return redirect()->route('admin.dashboard');
})->where('any', '.+');

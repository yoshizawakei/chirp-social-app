<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Mail\TestMail;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


// トップページ関係
Route::get("/", [ItemController::class, "index"])->name("top.index");
Route::get('/items/{item}', [ItemController::class, 'show'])->name('items.detail');

// Item関係
Route::middleware(["auth", "verified"])->group(function () {
    Route::get("/sell", [ItemController::class, "create"])->name("items.create");
    Route::post("/items", [ItemController::class, "store"])->name("items.store");
    Route::get("/items/{item}/edit", [ItemController::class, "edit"])->name("items.edit");
    Route::put("/items/{item}", [ItemController::class, "update"])->name("items.update");
    Route::delete("/items/{item}", [ItemController::class, "destroy"]);

    Route::get("/items/{item}/purchase", [ItemController::class, "purchase"])->name("items.purchase");

    Route::post("/items/{item}/purchase/checkout", [ItemController::class, "createCheckoutSession"])->name("items.createCheckoutSession");

    Route::get("/items/purchase/success", [ItemController::class, "purchaseSuccess"])->name("items.purchaseSuccess");

    Route::get("/items/thanks", function () {
        return view("items.thanks");
    })->name("items.thanks");
});

// mypage関係
Route::middleware("auth")->group(function () {
    Route::get("/mypage", [MypageController::class, "index"])->name("mypage.index");
    Route::get("/api/purchased-items", [MypageController::class, "getPurchasedItems"])->name("mypage.purchased_items");

    // Profile関係
    Route::get("/mypage/profile", [ProfileController::class, "index"])->name("mypage.profile");
    Route::post("profile/edit", [ProfileController::class, "edit"])->name("profile.edit");
    Route::get("profile/address-edit", [ProfileController::class, "addressEdit"])->name("profile.address.edit");
    Route::put("profile/address-update", [ProfileController::class, "addressUpdate"])->name("profile.address.update");
});

// Comment関係
Route::middleware("auth")->group(function () {
    Route::post("/items/{item}/comments", [CommentController::class, "store"])->name("comments.store");
    Route::delete("/comments/{comment}", [CommentController::class, "destroy"])->name("comments.destroy");
});

// Like関係
Route::middleware("auth")->group(function () {
    Route::post("/items/{item}/like", [LikeController::class, "toggle"])->name("items.like.toggle");
    Route::get("/mypage/likes", [LikeController::class, "index"])->name("mypage.likes");
});

// mylist関係
Route::middleware("auth")->get("/api/mylist", [MylistController::class, "index"]);


// メールテスト
Route::get("/send-test-mail", function () {
    $name = "テストユーザー";
    $message_body = "これはLaravelから送信されたテストメールです。";

    Mail::to("test@example")->send(new TestMail($name, $message_body));
    return "Test email sent successfully!";
});
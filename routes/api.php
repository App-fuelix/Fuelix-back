<?php
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

Route::post('/login', [AuthController::class,'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class,'logout']);
    Route::get('/me', [AuthController::class,'me']);

});

Route::post('/forgot-password', function (Request $request) {
    $request->validate(['email' => 'required|email']);

    $status = Password::sendResetLink(
        $request->only('email')
    );

    return response()->json([
        'message' => $status === Password::RESET_LINK_SENT
            ? 'Lien de réinitialisation envoyé par email'
            : 'Impossible d\'envoyer le lien de réinitialisation',
        'status' => $status
    ]);
})->name('password.email');

Route::post('/reset-password', function (Request $request) {
    $request->validate([
        'token'                 => ['required'],
        'email'                 => ['required', 'email'],
        'password'              => ['required', 'min:8', 'confirmed'],
        'password_confirmation' => ['required'],
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password)
            ])->setRememberToken(Str::random(60));

            $user->save();

            // Optionnel : Invalider tous les tokens existants (bonne pratique sécurité)
            $user->tokens()->delete();
        }
    );

    return $status === Password::PASSWORD_RESET
        ? response()->json([
            'message' => 'Mot de passe réinitialisé avec succès. Vous pouvez maintenant vous connecter.',
        ], 200)
        : response()->json([
            'message' => __($status),   // ex: "This password reset token is invalid."
            'error'   => true
        ], 400);
})->name('password.reset');   // Important pour que le mail contienne le bon lien
Route::get('/test', function () {
    return response()->json(["ok" => true]);
});

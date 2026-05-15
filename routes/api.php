<?php 
use App\Http\Controllers\Api\WaWebhookController;
use Illuminate\Support\Facades\Route;
   
   // route for catching location fro m whatsapp bot
   Route::post('/webhook/wa-checkout', [WaWebhookController::class, 'handleCheckout']);
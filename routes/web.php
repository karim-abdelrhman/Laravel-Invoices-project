 <?php

 use App\Http\Controllers\CustomerReportController;
 use App\Http\Controllers\HomeController;
 use App\Http\Controllers\InvoiceAttachmentsController;
 use App\Http\Controllers\InvoiceController;
 use App\Http\Controllers\InvoiceDetailsController;
 use App\Http\Controllers\InvoiceReportController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
 use App\Http\Controllers\RoleController;
 use App\Http\Controllers\SectionController;
 use App\Http\Controllers\UserController;
 use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/register', function () {
    return view('auth.register');
});

Route::get('/dashboard', [HomeController::class , 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::resource('invoices',InvoiceController::class);
Route::resource('sections',SectionController::class);
Route::resource('products',ProductController::class);
Route::resource('InvoiceAttachments',InvoiceAttachmentsController::class);

Route::get('/section/{id}' , [InvoiceController::class , 'getProducts']);
Route::get('/InvoicesDetails/{id}' , [InvoiceDetailsController::class , 'show']);
Route::get('View_file/{invoice_number}/{file_name}', [InvoiceDetailsController::class , 'open_file']);
 Route::get('download/{invoice_number}/{file_name}', [InvoiceDetailsController::class , 'get_file']);
Route::post('delete_file' , [InvoiceAttachmentsController::class , 'destroy'])->name('delete_file');
 Route::get('invoices_paid', [InvoiceController::class , 'invoicesPaid']);
 Route::get('invoices_partial', [InvoiceController::class , 'invoicesPartial']);
 Route::get('invoices_not_paid', [InvoiceController::class , 'invoicesNotPaid']);
 Route::get('print_invoice/{id}', [InvoiceController::class , 'printInvoice']);
 Route::get('markAsRead' , [InvoiceController::class , 'markAllAsRead'])->name('markAll');
 Route::get('invoices_archive' , [InvoiceController::class  , 'invoicesArchive'])->name('invoices_archive');

Route::post('/invoice_Update_status/{id}' , [InvoiceController::class , 'updateStatus'])->name('update_invoice_status');

 Route::group(['middleware' => ['auth']], function() {
     Route::resource('roles',RoleController::class);
     Route::resource('users',UserController::class);
     Route::post('users/destroy' , [UserController::class , 'destroy'])->name('users.destroy');
 });

 Route::get('invoices_reports' , [InvoiceReportController::class , 'index']);
 Route::get('customers_reports' , [CustomerReportController::class , 'index']);
 Route::post('invoice_search' , [InvoiceReportController::class , 'invoiceSearch']);
 Route::post('customer_search' , [CustomerReportController::class , 'customerSearch']);


Route::get('/{page}', [\App\Http\Controllers\AdminController::class , 'index']);

require __DIR__.'/auth.php';


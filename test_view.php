<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

try {
    $codes = \App\Models\Loan::query()->select('loan_code')->distinct()->paginate(10);
    $allLoans = \App\Models\Loan::with(['tool.location', 'tool.rack', 'borrower', 'keeper', 'item'])
        ->whereIn('loan_code', $codes->pluck('loan_code'))
        ->get()
        ->groupBy('loan_code');

    echo view('transaction.loan.index', [
        'loans' => $codes,
        'allLoans' => $allLoans,
        'activeLoanDocs' => 0,
        'itemsOnLoan' => 0
    ])->render();
    echo "\n\nSUCCESS\n";
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine() . "\n";
}

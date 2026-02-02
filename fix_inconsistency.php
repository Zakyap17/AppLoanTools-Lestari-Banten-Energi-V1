$inUseItems = \App\Models\ToolItem::where('status', 'in_use')->get();
$fixed = 0;
foreach($inUseItems as $item){
$loan = \App\Models\Loan::where('tool_item_id', $item->id)->whereIn('status', ['ongoing', 'overdue'])->first();
if(!$loan){
$item->update(['status' => 'available']);
$fixed++;
}
}
echo "Fixed $fixed orphan items.";
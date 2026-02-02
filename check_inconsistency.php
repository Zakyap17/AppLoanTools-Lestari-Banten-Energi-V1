$inUseItems = \App\Models\ToolItem::where('status', 'in_use')->get();
$orphans = [];
foreach($inUseItems as $item){
$loan = \App\Models\Loan::where('tool_item_id', $item->id)->whereIn('status', ['ongoing', 'overdue'])->first();
if(!$loan){
$orphans[] = $item->serial_number;
}
}
echo "Orphaned In Use Items: " . implode(', ', $orphans) . "\nTotal: " . count($orphans);
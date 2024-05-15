<?php

namespace App\Imports;

use App\Http\Controllers\Util\FormatPhoneNumberUtil;
use App\Http\Controllers\Util\PasswordGeneratorUtil;
use App\Http\Controllers\Util\SMSUtil;
use App\Inventory;
use App\InventoryHistory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class InventoryImport implements ToCollection, WithStartRow
{

    private $rows = 0;
    private $message = "";

    protected $merchant_id;
    public function __construct(string $merchant_id)
    {
        $this->merchant_id = $merchant_id;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row)
        {
            $sku = $row[1];
            $name = $row[2];
            $description = $row[3];
            $quantity = $row[4];
            $low_count = $row[5];
            $amount = $row[6];

            $inventory = DB::table('inventories')
                ->where('sku', $sku)
                ->first();
            if ($inventory) {

                $balance = $inventory->quantity + $quantity;
                $update = DB::table('inventories')
                    ->where('id', $inventory->id)
                    ->update([
                        'amount' => $amount,
                        'quantity' => $balance,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                if($update){

                    $inventory_history_object = new InventoryHistory();
                    $inventory_history_created = $inventory_history_object->create([
                        'inventory_id' => $inventory->id,
                        'transaction_type' => 1,
                        'quantity' => $quantity,
                        'balance' => $balance,
                    ]);

                    ++$this->rows;
                }

            }else{

                $inventory_object = new Inventory();
                $inventory_created = $inventory_object->create([
                    'sku' => $sku,
                    'name' => $name,
                    'description' => $description,
                    'amount' => $amount,
                    'low_count' => $low_count,
                    'quantity' => $quantity,
                    'merchant_id' => $this->merchant_id,
                ]);

                if ($inventory_created) {

                    ++$this->rows;

                }
            }

        }

    }

    public function getRowCount(): int
    {
        return $this->rows;
    }

    public function getErrorMessage(): string
    {
        return $this->message;
    }

    /**
     * @return int
     */
    public function startRow(): int
    {
        return 4;
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if (!in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        DB::statement("ALTER TABLE orders MODIFY status ENUM('baru','dicuci','disetrika','dikerjakan','siap_diambil','selesai') NOT NULL DEFAULT 'baru'");
        DB::statement("UPDATE orders SET status = 'dikerjakan' WHERE status IN ('dicuci','disetrika')");
        DB::statement("ALTER TABLE orders MODIFY status ENUM('baru','dikerjakan','siap_diambil','selesai') NOT NULL DEFAULT 'baru'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if (!in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        DB::statement("ALTER TABLE orders MODIFY status ENUM('baru','dicuci','disetrika','dikerjakan','siap_diambil','selesai') NOT NULL DEFAULT 'baru'");
        DB::statement("UPDATE orders SET status = 'dicuci' WHERE status = 'dikerjakan'");
        DB::statement("ALTER TABLE orders MODIFY status ENUM('baru','dicuci','disetrika','siap_diambil','selesai') NOT NULL DEFAULT 'baru'");
    }
};

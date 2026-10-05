<?php

namespace App\Models;

class Mahasiswa
{
    private string $nim;
    private string $nama;
    private string $prodi;

    public function __construct(string $nim, string $nama, string $prodi)
    {
        $this->nim = strtoupper(trim($nim));
        $this->nama = trim($nama);
        $this->prodi = trim($prodi);
    }

    public function getNim(): string
    {
        return $this->nim;
    }

    public function getNama(): string
    {
        return $this->nama;
    }

    public function getProdi(): string
    {
        return $this->prodi;
    }

    public function getAngkatan(): string
    {
        $tahun = null;

        if (preg_match('/^(?:[A-Za-z]\d{2})?(\d{2})/', $this->nim, $matches)) {
            $tahun = $matches[1];
        }

        return $tahun === null ? '-' : '20' . $tahun;
    }
}
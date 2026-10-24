<?php

namespace App\Controllers;

use App\Models\Buku;
use JsonException;
use stdClass;

class BukuController
{
    private Buku $model;

    public function __construct()
    {
        $this->model = new Buku();
    }

    public function index(): void
    {
        $this->respond(200, [
            'success' => true,
            'message' => 'Data buku berhasil diambil',
            'data' => $this->model->all(),
        ]);
    }

    public function show(int $id): void
    {
        $book = $this->model->find($id);

        if ($book === null) {
            $this->notFound();
        }

        $this->respond(200, [
            'success' => true,
            'message' => 'Data buku berhasil diambil',
            'data' => $book,
        ]);
    }

    public function store(): void
    {
        $book = $this->model->create($this->validatedInput());

        $this->respond(201, [
            'success' => true,
            'message' => 'Data buku berhasil ditambahkan',
            'data' => $book,
        ]);
    }

    public function update(int $id): void
    {
        if ($this->model->find($id) === null) {
            $this->notFound();
        }

        $book = $this->model->update($id, $this->validatedInput());

        $this->respond(200, [
            'success' => true,
            'message' => 'Data buku berhasil diperbarui',
            'data' => $book,
        ]);
    }

    public function destroy(int $id): void
    {
        $book = $this->model->find($id);

        if ($book === null) {
            $this->notFound();
        }

        $this->model->delete($id);

        $this->respond(200, [
            'success' => true,
            'message' => 'Data buku berhasil dihapus',
            'data' => $book,
        ]);
    }

    private function validatedInput(): array
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if (!preg_match('#^application/json(?:\s*;|$)#i', $contentType)) {
            $this->respond(415, [
                'success' => false,
                'message' => 'Content-Type harus application/json',
                'data' => null,
            ]);
        }

        $rawBody = file_get_contents('php://input');

        if (!is_string($rawBody)) {
            $this->respond(400, [
                'success' => false,
                'message' => 'Body request tidak dapat dibaca',
                'data' => null,
            ]);
        }

        try {
            $input = json_decode($rawBody, false, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            $this->respond(400, [
                'success' => false,
                'message' => 'Format JSON tidak valid',
                'data' => null,
            ]);
        }

        if (!$input instanceof stdClass) {
            $this->respond(400, [
                'success' => false,
                'message' => 'Body request harus berupa object JSON',
                'data' => null,
            ]);
        }

        $input = get_object_vars($input);
        $judul = isset($input['judul']) && is_string($input['judul'])
            ? trim($input['judul'])
            : '';
        $penulis = isset($input['penulis']) && is_string($input['penulis'])
            ? trim($input['penulis'])
            : '';
        $penerbit = isset($input['penerbit']) && is_string($input['penerbit'])
            ? trim($input['penerbit'])
            : '';
        $tahunTerbit = filter_var($input['tahun_terbit'] ?? null, FILTER_VALIDATE_INT);

        if (
            $judul === '' || strlen($judul) > 150 ||
            $penulis === '' || strlen($penulis) > 100 ||
            $penerbit === '' || strlen($penerbit) > 100 ||
            $tahunTerbit === false || $tahunTerbit < 1000 || $tahunTerbit > 65535
        ) {
            $this->respond(400, [
                'success' => false,
                'message' => 'Field judul, penulis, penerbit, dan tahun_terbit wajib valid',
                'data' => null,
            ]);
        }

        return [
            'judul' => $judul,
            'penulis' => $penulis,
            'penerbit' => $penerbit,
            'tahun_terbit' => $tahunTerbit,
        ];
    }

    private function notFound(): void
    {
        $this->respond(404, [
            'success' => false,
            'message' => 'Data buku tidak ditemukan',
            'data' => null,
        ]);
    }

    private function respond(int $status, array $body): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        echo json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}
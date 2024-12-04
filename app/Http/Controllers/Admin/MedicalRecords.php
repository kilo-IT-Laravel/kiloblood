<?php

namespace App\Http\Controllers\Admin;

use App\Koobeni;
use App\Models\DocumentationFile;
use Exception;

class MedicalRecords extends Koobeni
{
    public function index()
    {
        try {
            $data = $this->findAll->allWithPagination([
                'model' => DocumentationFile::class,
                'sort' => 'latest',
                'perPage' => $this->req->perPage,
                'relations' => ['user:id,name,image,email'],
                'select' => [
                    'id',
                    'user_id',
                    'file_path',
                    'file_type'
                ],
                'search' => [
                    'user.name' => $this->req->search,
                    'file_type' => $this->req->fileType
                ],
                'dateRange' => [
                    'startDate' => $this->req->startDate,
                    'endDate' => $this->req->endDate
                ]
            ]);
            return $this->paginationDataResponse($data);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function show(int $id)
    {
        try {
            $data = DocumentationFile::findOrFail($id)->load('users:id,name,image,email');
            return $this->dataResponse($data);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function delete(int $id)
    {
        try {
            DocumentationFile::findOrFail($id)->delete;
            return $this->dataResponse(null);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function restore(int $id)
    {
        try {
            DocumentationFile::withTrashed()->findOrFail($id)->restore();
            return $this->dataResponse(null);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function trashed()
    {
        try {
            $data = $this->findAll->allWithPagination([
                'model' => DocumentationFile::class,
                'sort' => 'latest',
                'trash' => true,
                'perPage' => $this->req->perPage,
                'model' => DocumentationFile::class,
                'sort' => 'latest',
                'perPage' => $this->req->perPage,
                'relations' => ['user:id,name,image,email'],
                'select' => [
                    'id',
                    'user_id',
                    'file_path',
                    'file_type'
                ],
                'search' => [
                    'user.name' => $this->req->search,
                    'file_type' => $this->req->fileType
                ],
                'dateRange' => [
                    'startDate' => $this->req->startDate,
                    'endDate' => $this->req->endDate
                ]
            ]);
            return $this->paginationDataResponse($data);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function forceDelete(int $id)
    {
        try {
            DocumentationFile::withTrashed()->findOrFail($id)->forceDelete();
            return $this->dataResponse(null);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'folder_id',
        'original_name',
        'stored_name',
        'disk_path',
        'mime_type',
        'file_size',
        'uploaded_by',
        'document_type',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(EmployeeFolder::class, 'folder_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Human-readable file size string (e.g. "2.4 MB")
     */
    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes < 1024)       return $bytes . ' B';
        if ($bytes < 1048576)    return round($bytes / 1024, 1) . ' KB';
        if ($bytes < 1073741824) return round($bytes / 1048576, 1) . ' MB';
        return round($bytes / 1073741824, 1) . ' GB';
    }

    /**
     * Simple type label for display (PDF, Word, Image, etc.)
     */
    public function getTypeLabel(): string
    {
        $mime = strtolower($this->mime_type ?? '');
        if (str_contains($mime, 'pdf'))                             return 'PDF';
        if (str_contains($mime, 'word') || str_contains($mime, 'officedocument.wordprocessing')) return 'Word';
        if (str_contains($mime, 'excel') || str_contains($mime, 'spreadsheet'))                  return 'Excel';
        if (str_contains($mime, 'powerpoint') || str_contains($mime, 'presentation'))            return 'PowerPoint';
        if (str_contains($mime, 'image/'))                         return 'Image';
        if (str_contains($mime, 'text/'))                          return 'Text';
        if (str_contains($mime, 'zip') || str_contains($mime, 'rar') || str_contains($mime, '7z')) return 'Archive';
        return 'File';
    }
}

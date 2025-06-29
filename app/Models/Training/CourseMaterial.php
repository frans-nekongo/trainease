<?php

namespace App\Models\Training;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tonysm\RichTextLaravel\Casts\AsRichTextContent;

class CourseMaterial extends Model
{
    /** @use HasFactory<\Database\Factories\Training\CourseMaterialFactory> */
    use HasFactory;

    protected $fillable = [
        'course_id',
        'material_name',
        'material_content',
        'description',
    ];

    protected $casts = [
        'material_content' => AsRichTextContent::class,
        'quiz_data' => 'array', // Automatically decode JSON quizzes
    ];

    /**
     * @return BelongsTo<Course, $this>
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    /**
     * @param  int  $userId
     */
    public function isCompletedByUser($userId): bool
    {
        return $this->progress()->where('user_id', $userId)->where('status', 'completed')->exists();
    }

    /**
     * @return HasMany<CourseProgress, $this>
     */
    public function progress(): HasMany
    {
        return $this->hasMany(CourseProgress::class);
    }
}

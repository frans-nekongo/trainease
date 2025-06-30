<?php

use Livewire\Volt\Component;
use App\Models\Training\CourseMaterial;
use App\Models\Training\Course;

new class extends Component {
    public $course;
    public $courseMaterials = [];
    public string $materialTitle = '';
    public string $materialDescription = '';
    public array $contentBlocks = [];
    public ?int $editingMaterialId = null;

    public function mount($course)
    {
        $this->course = Course::find($course);
        if ($this->course) {
            $this->courseMaterials = $this->course->materials ?? [];
            if ($firstMaterial = $this->courseMaterials->first()) {
                $this->loadMaterial($firstMaterial->id);
            }
        }
    }

    public function addContentBlock($type)
    {
        $this->contentBlocks[] = ['type' => $type, 'content' => ''];
    }

    public function removeContentBlock($index)
    {
        unset($this->contentBlocks[$index]);
        $this->contentBlocks = array_values($this->contentBlocks);
    }

    public function loadMaterial($materialId)
    {
        $material = CourseMaterial::find($materialId);
        if ($material) {
            $this->materialTitle = $material->material_name;
            $this->materialDescription = $material->description;
            $this->editingMaterialId = $material->id;

            // Convert Markdown to content blocks
            $this->contentBlocks = $this->parseMarkdown($material->material_content);
        }
    }

    public function updateMaterial()
    {
        if (!$this->editingMaterialId) {
            return;
        }

        $material = CourseMaterial::find($this->editingMaterialId);
        if ($material) {
            $markdownContent = $this->generateMarkdown();
            $material->update([
                'material_name' => $this->materialTitle,
                'description' => $this->materialDescription,
                'material_content' => $markdownContent,
            ]);

            session()->flash('message', 'Course material updated successfully!');
        }
    }

    private function parseMarkdown($markdown)
    {
        $blocks = [];
        $lines = explode("\n", $markdown);

        foreach ($lines as $line) {
            if (str_starts_with($line, '# ')) {
                $blocks[] = ['type' => 'heading1', 'content' => substr($line, 2)];
            } elseif (str_starts_with($line, '## ')) {
                $blocks[] = ['type' => 'heading2', 'content' => substr($line, 3)];
            } elseif (str_starts_with($line, '### ')) {
                $blocks[] = ['type' => 'heading3', 'content' => substr($line, 4)];
            } else {
                $blocks[] = ['type' => 'text', 'content' => $line];
            }
        }

        return $blocks;
    }

    private function generateMarkdown()
    {
        $markdown = [];
        foreach ($this->contentBlocks as $block) {
            switch ($block['type']) {
                case 'heading1':
                    $markdown[] = '# ' . $block['content'];
                    break;
                case 'heading2':
                    $markdown[] = '## ' . $block['content'];
                    break;
                case 'heading3':
                    $markdown[] = '### ' . $block['content'];
                    break;
                case 'text':
                    $markdown[] = $block['content'];
                    break;
            }
        }

        return implode("\n", $markdown);
    }
};

?>

<div>
    <div class="flex min-h-screen bg-slate-100 dark:bg-slate-900">
        <!-- Sidebar with materials and Add button -->
        <aside class="w-80 border-r dark:border-gray-700 p-6 overflow-y-auto bg-white dark:bg-gray-800">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Course Materials</h2>
            @foreach ($courseMaterials as $material)
                <div class="cursor-pointer border rounded-md p-3 mb-2 hover:bg-blue-50 dark:hover:bg-gray-700"
                     :class="{{ $editingMaterialId }} === {{ $material->id }} ? 'bg-blue-100 dark:bg-blue-800 text-blue-900 dark:text-white font-semibold' : 'bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-200'"
                     wire:click="loadMaterial({{ $material->id }})">
                    <span>{{ $material->material_name }}</span>
                </div>
            @endforeach
        </aside>

        <!-- Editor Content -->
        <div class="flex-1 flex flex-col w-full">
            <!-- Title Textarea -->
            <div class="w-fullsticky top-0 z-10 flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm max-w-full">
                <input type="text" wire:model.lazy="materialTitle" placeholder="Material Title"
                       class="w-full text-xl font-semibold bg-transparent focus:outline-none text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500" />
            </div>

            <!-- Description Textarea -->
            <div class="px-6 py-2 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900">
                <textarea wire:model.lazy="materialDescription" placeholder="Material Description"
                          class="w-full text-sm bg-transparent focus:outline-none text-gray-700 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-500" rows="3"></textarea>
            </div>

            <!-- Content Blocks -->
            <div class="px-6 py-2 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900">
                @foreach ($contentBlocks as $index => $block)
                    <div class="flex items-center mb-2">
                        @if ($block['type'] === 'heading1')
                            <input type="text" wire:model="contentBlocks.{{ $index }}.content" class="w-full text-2xl font-bold bg-transparent focus:outline-none text-gray-900 dark:text-white" placeholder="Heading 1" />
                        @elseif ($block['type'] === 'heading2')
                            <input type="text" wire:model="contentBlocks.{{ $index }}.content" class="w-full text-xl font-bold bg-transparent focus:outline-none text-gray-900 dark:text-white" placeholder="Heading 2" />
                        @elseif ($block['type'] === 'heading3')
                            <input type="text" wire:model="contentBlocks.{{ $index }}.content" class="w-full text-lg font-bold bg-transparent focus:outline-none text-gray-900 dark:text-white" placeholder="Heading 3" />
                        @else
                            <textarea wire:model="contentBlocks.{{ $index }}.content" class="w-full text-sm bg-transparent focus:outline-none text-gray-700 dark:text-gray-200" placeholder="Start writing..."></textarea>
                        @endif
                        <button wire:click="removeContentBlock({{ $index }})" class="ml-2 text-red-500">
                            Remove
                        </button>
                    </div>
                @endforeach
            </div>

            <div class="p-6">
                <button wire:click="addContentBlock('heading1')" class="px-4 py-2 bg-blue-500 text-white rounded">Add Heading 1</button>
                <button wire:click="addContentBlock('heading2')" class="px-4 py-2 bg-blue-500 text-white rounded">Add Heading 2</button>
                <button wire:click="addContentBlock('heading3')" class="px-4 py-2 bg-blue-500 text-white rounded">Add Heading 3</button>
                <button wire:click="addContentBlock('text')" class="px-4 py-2 bg-gray-500 text-white rounded">Add Text</button>
                <button wire:click="updateMaterial" class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg shadow-md">
                    Save Material
                </button>
            </div>
        </div>
    </div>
</div>
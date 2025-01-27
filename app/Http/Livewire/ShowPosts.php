<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Post;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ShowPosts extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $search;
    public $post;
    public $image;
    public $identificador;
    public $sort = 'id';
    public $direction = 'desc';
    public $cantidad = '10';

    public $open_edit = false;

    protected $queryString = [
        'cantidad' => ['except' => '10'], 
        'sort' => ['except' => 'id'], 
        'direction' => ['except' => 'desc'], 
        'search' => ['except' => '']
    ];

    public function mount()
    {
        $this->identificador = rand();
        $this->post = new Post();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    protected $rules = [
        'post.title' => 'required',
        'post.content' => 'required',
    ];

    // Oyente
    protected $listeners = [ 
        'render' => 'render',
        'delete' => 'delete'
    ];

    public function render()
    {
        $posts =    Post::where('title', 'like', '%' . $this->search . '%')->
                    orWhere('content', 'like', '%' . $this->search . '%')->
                    orderBy($this->sort, $this->direction)->paginate($this->cantidad);
 
        return view('livewire.show-posts', compact('posts')); 
    }

    public function order($sort)
    {
        if ($this->sort === $sort) {
            if ($this->direction === 'desc') {
                $this->direction = 'asc';
            } else {
                $this->direction = 'desc';
            }
        } else {
            $this->sort = $sort;
            $this->direction = 'asc';
        }
    }

    public function edit(Post $post) 
    {
        $this->post = $post;
        $this->open_edit = true;
    }

    public function update() 
    {
        $this->validate();

        if ($this->image) {
            // Storage::delete([$this->post->image]);
            $this->post->image = $this->image->store('posts');
        }

        $this->post->save();
        $this->reset(['open_edit', 'image']);
        $this->identificador = rand();
        // $this->emitTo('show-posts', 'render');
        $this->emit('alert', '¡El post se actualizó correctamente!');
    }

    public function delete(Post $post)
    {
        $post->delete();
    }
}

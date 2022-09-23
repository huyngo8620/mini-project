<?php 
class Posts extends Controller{

    public function __construct()
    {
        if(!isLoggedIn()){
            redirect('users/login');
        }

        $this->postModel = $this->model('Post');
        $this->userModel = $this->model('User');
    }

    public function index(){
        $posts = $this->postModel->getPosts();
        $data = [
            'posts' => $posts
        ];

        $this->view('posts/index', $data);
    }

    //add 
    public function add(){
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $data = [
                'title' => trim($_POST['title']),
                'body' => trim($_POST['body']),
                'user_id' => $_SESSION['user_id'],
                'title_err' => '',
                'body_err' => '',
            ];

            if(empty($data['title'])){
                $data['title_err'] = 'Please enter post title';
            }
            if(empty($data['body'])){
                $data['body_err'] = 'Please enter the post content';
            }

            if(empty($data['title_err']) && empty($data['body_err'])){
                if($this->postModel->addPost($data)){
                    flash('post_message', 'Added  post successfully');
                    redirect('posts');
                }else{
                    die('something went wrong');
                }
            }else{
                $this->view('posts/add', $data);
            }
        }else{
            $data = [
                'title' => (isset($_POST['title']) ? trim($_POST['title']) : ''),
                'body' =>  (isset($_POST['body'])? trim($_POST['body']) : '')
            ];

            $this->view('posts/add', $data);
        }
    }

    //show
    public function show($id){
        $post = $this->postModel->getPostById($id);
        $user = $this->userModel->getUserById($post->user_id);

        $data = [
            'post' => $post,
            'user' => $user
        ];

        $this->view('posts/show', $data);
    }

     //edit
     public function edit($id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $data = [
                'id' => $id,
                'title' => trim($_POST['title']),
                'body' => trim($_POST['body']),
                'user_id' => $_SESSION['user_id'],
                'title_err' => '',
                'body_err' => '',
            ];

            if(empty($data['title'])){
                $data['title_err'] = 'Please enter post title';
            }

            if(empty($data['body'])){
                $data['body_err'] = 'Please enter the post content';
            }

            //validate error
            if(empty($data['title_err']) && empty($data['body_err'])){
                if($this->postModel->updatePost($data)){
                    flash('post_message', 'Updated post successfully');
                    redirect('posts');
                }else{
                    die('something went wrong');
                }
               
            }else{
                $this->view('posts/edit', $data);
            }
        }else{
            $post = $this->postModel->getPostById($id);
            if($post->user_id != $_SESSION['user_id']){
                redirect('posts');
            }
            $data = [
                'id' => $id,
                'title' => $post->title,
                'body' => $post->body
            ];

            $this->view('posts/edit', $data);
        }
    }
    
    //delete
    public function delete($id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $post = $this->postModel->getPostById($id);
            if($post->user_id != $_SESSION['user_id']){
                redirect('posts');
            }
            
            if($this->postModel->deletePost($id)){
                flash('post_message', 'Post Removed');
                redirect('posts');
            }else{
                die('something went wrong');
            }
        }else{
            redirect('posts');
        }
    }
}

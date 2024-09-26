<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Models\Follow;
use App\Events\ExampleEvent;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\View;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;


class UserController extends Controller
{
    public function registerUser(Request $request){
        $incommingFields=$request->validate([
            'username'=>['required','min:3','max:20',Rule::unique('users','username')],
            'email'=>['required','email',Rule::unique('users','email')],
            'password'=>['required','min:6','confirmed']
        ]);

        //Hashing password  check default
        // $incommingFields['password']=bcrypt($incommingFields['password']);
       $user = User::create($incommingFields);
    
       //loging in the user immediatelly
        auth()->login($user);
        return redirect('/')->with('success','thank you for registering!');

    }

    public function loginApi(Request $request){

        $incommingFields=$request->validate([
            'username'=>['required'],
            'password'=>['required']
        ]);

    
        if(auth()->attempt($incommingFields)){
            $user = User::where('username',$incommingFields['username'])->first();

           $token = $user->createToken('myapptoken')->plainTextToken;

           return ['token'=>$token];
        }
        return 'login failed';
    }

    public function login(Request $request){
        $incommingFields=$request->validate([
            'loginusername'=>['required'],
            'loginpassword'=>['required']
        ]);

        if(auth()->attempt(['username'=>$incommingFields['loginusername'],
        'password'=>$incommingFields['loginpassword']])){

            $request->session()->regenerate();
            event(new ExampleEvent(['username'=>auth()->user()->username,'action'=>'login']));
            return redirect('/')->with('success','you have successfully loged in!');

        }
      
        return redirect('/')->with('failure','invalid credentials');

    }

    public function logout(){
        event(new ExampleEvent(['username'=>auth()->user()->username,'action'=>'logout']));
        auth()->logout();
        return redirect('/')->with('success','you are now logged out!');
    }


    public function showCorrectHomePage() {

        if(auth()->check()){
            return view('home-page-feed',['posts'=>auth()->user()->feedPosts()->latest()->paginate(4)]);

        }
        // if(Cache::has('postCount')){
        //     $postCount = Cache::get('postCount');

        // }else{

        //     // sleep(5);
        //     $postCount = Post::count();
        //     Cache::put('postCount',$postCount,20);
        // }
        //BETER WAY
        $postCount = Cache::remember('postCount',20,function(){
            // sleep(5);
            return Post::count();
        });

         return view('home',['postCount'=>$postCount]);
        
        
    }

    public function profile(User $user) {

        $this->getSharedData($user);
        return view('profile-post',['posts'=>$user->posts()->latest()->get()]);
 
    }

    public function showAvatarForm(){

        return view('avatar-form');
    }

    public function saveAvatar(Request $request){


         //DIRECTLY SAVING TO STORAGE NOT GOOD
        // $request->file('avatar')->store('/public/avatars');

        $request->validate([
            'avatar'=>'required|image|max:3000'
        ]);

        $user = auth()->user();
        $filename = $user->id ."_" . uniqid().".jpg";

        $manager = new ImageManager(new Driver());
        $image = $manager->read($request->file('avatar'));
        $imageData = $image->cover(120,120)->toJpeg();
        Storage::put('/public/avatars/'.$filename,$imageData);

        $oldAvatar = $user->avatar;

        $user ->avatar = $filename;
        $user->save();

        if($oldAvatar != "/fallback-avatar.jpg"){

            Storage::delete(str_replace('/storage/','public/',$oldAvatar));
        }

        return back()->with('success','Congrats for your new avatar!');
        

    }

    public function followers(User $user) {

        $this->getSharedData($user);

        return view('profile-followers',['followers'=>$user->followers()->latest()->get()]);
 

    }

    public function following(User $user) {

        $this->getSharedData($user);

       return view('profile-following',['following'=>$user->followingUsers()->latest()->get()]);

    }


    private function getSharedData($user){

        $currentlyFolowing = 0;

        if(auth()->check()){

            $currentlyFolowing =  Follow::where([['user_id','=',auth()->user()->id],['followeduser','=',$user->id]])->count();

        }

        View::share('sharedData',['currentlyFolowing'=>$currentlyFolowing,'username'=>$user->username,'avatar'=>$user->avatar,'postCount'=>$user->posts()->count(), 'followerCount' => $user->followers()->count(), 'followingCount' => $user->followingUsers()->count()]);
    }

}

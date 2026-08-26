<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Archivo;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Course;
use App\Models\Disclaimer;
use App\Models\Education;
use App\Models\Employment as EmploymentModel;
use App\Models\Event;
use App\Models\Form;
use App\Models\Industry;
use App\Models\Information;
use App\Models\Military;
use App\Models\Post;
use App\Models\Reference;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PublicSiteController extends Controller
{
    public function home(): JsonResponse
    {
        $categories = Category::orderBy('orden')->get();
        $posts = Post::latest('id')->limit(4)->get();

        return $this->ok([
            'hero' => [
                'title' => 'YOUR SECURITY IS OUR BUSINESS',
                'subtitle' => 'Veteran owned and operated security solutions for clients who expect reliability, professionalism, and active site awareness.',
                'primary_cta' => ['label' => 'GET A QUOTE', 'url' => '/contact'],
                'secondary_cta' => ['label' => 'EXPLORE SERVICES', 'url' => '/services'],
            ],
            'about' => [
                'title' => 'ABOUT US',
                'summary' => 'TAP Security was founded with the vision to challenge the security service industry by proactively placing the client needs first.',
            ],
            'featured_categories' => $categories->map(fn (Category $category) => $this->transformCategory($category))->values(),
            'featured_posts' => $posts->map(fn (Post $post) => $this->transformPostCard($post))->values(),
        ]);
    }

    public function homePosts(): JsonResponse
    {
        return $this->ok(Post::latest('id')->limit(4)->get()->map(fn (Post $post) => $this->transformPostCard($post))->values());
    }

    public function homeCategories(): JsonResponse
    {
        return $this->ok(Category::orderBy('orden')->get()->map(fn (Category $category) => $this->transformCategory($category))->values());
    }

    public function services(): JsonResponse
    {
        return $this->ok([
            ['title' => 'Armed / Unarmed Security', 'slug' => 'armed-unarmed-security'],
            ['title' => 'Access Control', 'slug' => 'access-control'],
            ['title' => 'Security Patrol', 'slug' => 'security-patrol'],
            ['title' => 'Personal Protection Officers', 'slug' => 'personal-protection-officers'],
            ['title' => 'Off-Duty Police Security and Traffic Control', 'slug' => 'off-duty-police-security-and-traffic-control'],
        ]);
    }

    public function industries(): JsonResponse
    {
        $industries = Industry::with('Category')->orderBy('orden')->get();

        return $this->ok($industries->map(fn (Industry $industry) => [
            'id' => $industry->id,
            'name' => $industry->titulo,
            'slug' => $industry->slug,
            'summary' => Str::limit(strip_tags($industry->contenido ?? ''), 160, '...'),
            'card_url' => $this->assetUrl($industry->card),
            'banner_url' => $this->assetUrl($industry->banner),
            'category' => $industry->Category ? $this->transformCategory($industry->Category) : null,
        ])->values());
    }

    public function industriesByCategory(string $categorySlug): JsonResponse
    {
        $category = Category::where('slug', $categorySlug)->firstOrFail();
        $industries = Industry::where('category_id', $category->id)->orderBy('orden')->get();

        return $this->ok([
            'category' => $this->transformCategory($category),
            'industries' => $industries->map(fn (Industry $industry) => $this->transformIndustryCard($industry))->values(),
            'all_categories' => Category::orderBy('orden')->get()->map(fn (Category $item) => $this->transformCategory($item))->values(),
        ]);
    }

    public function industryDetail(string $categorySlug, string $industrySlug): JsonResponse
    {
        $category = Category::where('slug', $categorySlug)->firstOrFail();
        $industry = Industry::where('category_id', $category->id)->where('slug', $industrySlug)->firstOrFail();

        return $this->ok([
            'category' => $this->transformCategory($category),
            'industry' => [
                'id' => $industry->id,
                'name' => $industry->titulo,
                'slug' => $industry->slug,
                'banner_url' => $this->assetUrl($industry->banner),
                'card_url' => $this->assetUrl($industry->card),
                'content_html' => $industry->contenido,
                'summary' => Str::limit(strip_tags($industry->contenido ?? ''), 160, '...'),
            ],
            'all_categories' => Category::orderBy('orden')->get()->map(fn (Category $item) => $this->transformCategory($item))->values(),
        ]);
    }

    public function trainingEvents(): JsonResponse
    {
        return $this->ok(Event::orderBy('start_date')->get()->map(fn (Event $event) => [
            'id' => $event->id,
            'title' => $event->title,
            'slug' => $event->slug,
            'price' => $event->price,
            'duration' => $event->duration,
            'excerpt' => $event->excerpt,
            'description_html' => $event->description,
            'start_date' => $event->start_date,
            'end_date' => $event->end_date,
            'start_hour' => $event->start_hour,
            'formatted_date' => $event->start_date ? Carbon::parse($event->start_date)->format('M d, Y') : null,
        ])->values());
    }

    public function blog(): JsonResponse
    {
        return $this->ok(Post::latest('id')->get()->map(fn (Post $post) => $this->transformPostCard($post))->values());
    }

    public function blogDetail(string $slug): JsonResponse
    {
        $post = Post::where('slug', $slug)->firstOrFail();
        $related = Post::whereKeyNot($post->id)->latest('id')->limit(3)->get();

        return $this->ok([
            'id' => $post->id,
            'title' => $post->titulo,
            'slug' => $post->slug,
            'summary' => $post->resumen,
            'content_html' => $post->contenido,
            'card_image_url' => $this->assetUrl($post->card),
            'banner_image_url' => $this->assetUrl($post->banner),
            'published_at' => optional($post->created_at)->toISOString(),
            'formatted_date' => optional($post->created_at)->format('M d, Y'),
            'related_posts' => $related->map(fn (Post $relatedPost) => $this->transformPostCard($relatedPost))->values(),
        ]);
    }

    public function courses(): JsonResponse
    {
        return $this->ok(Course::latest('id')->get()->map(fn (Course $course) => $this->transformCourseCard($course))->values());
    }

    public function courseDetail(string $slug): JsonResponse
    {
        $course = Course::with(['chapters.chaptercontents', 'chapters.chapterquizzes'])->where('slug', $slug)->firstOrFail();
        $related = Course::whereKeyNot($course->id)->latest('id')->limit(3)->get();

        return $this->ok([
            ...$this->transformCourseCard($course),
            'subtitle' => $course->subtitulo,
            'content_html' => $course->contenido,
            'video_url' => $this->assetUrl($course->video),
            'audio' => $course->audio,
            'language' => $course->language,
            'available_from' => $course->disponible,
            'formatted_available' => $course->disponible ? Carbon::parse($course->disponible)->format('M d, Y') : null,
            'access_days' => $course->tiempovalido !== null ? (int) $course->tiempovalido : null,
            'instructor' => $course->responsable,
            'chapters' => $course->chapters->sortBy('order')->values()->map(fn ($chapter) => [
                'id' => $chapter->id,
                'title' => $chapter->title,
                'slug' => $chapter->slug,
                'order' => $chapter->order,
                'has_video' => filled($chapter->video),
                'has_audio' => (bool) $chapter->audio,
                'has_reading' => (bool) $chapter->reading,
                'has_quiz' => $chapter->chapterquizzes->isNotEmpty() || filled($chapter->quiz),
                'contents' => $chapter->chaptercontents->map(fn ($content) => [
                    'id' => $content->id,
                    'title' => $content->titulo,
                    'slug' => $content->slug,
                    'order' => $content->order,
                    'video_url' => $this->assetUrl($content->video),
                    'audio_url' => $this->assetUrl($content->audio),
                    'content_html' => $content->contenido,
                ])->values(),
            ]),
            'related_courses' => $related->map(fn (Course $relatedCourse) => $this->transformCourseCard($relatedCourse))->values(),
        ]);
    }

    public function contact(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'message' => ['required', 'string', 'min:10'],
            'origen' => ['nullable', 'string', 'max:100'],
        ]);

        $contact = new Contact();
        $contact->name = $validated['name'];
        $contact->email = $validated['email'];
        $contact->phone = $validated['phone'] ?? null;
        $contact->message = $validated['message'];
        $contact->origen = $validated['origen'] ?? 'api';
        $contact->save();

        $this->sendRawNotification('TAP contact request', $this->contactEmailText($contact));

        return response()->json(['success' => true, 'message' => 'Contact request submitted successfully.', 'data' => ['id' => $contact->id]], 201);
    }

    public function employment(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'lastname' => ['required', 'string', 'max:100'],
            'firstname' => ['required', 'string', 'max:100'],
            'mi' => ['nullable', 'string', 'max:10'],
            'date' => ['nullable', 'date'],
            'address' => ['required', 'string', 'max:255'],
            'apartment' => ['nullable', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'zipcode' => ['required', 'string', 'max:20'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255'],
            'birthday' => ['nullable', 'date'],
            'socialnumber' => ['nullable', 'string', 'max:20'],
            'placebirth' => ['nullable', 'string', 'max:100'],
            'appliedpay' => ['nullable', 'string', 'max:255'],
            'whichshift' => ['nullable', 'string'],
            'days' => ['nullable', 'array'],
            'citizen' => ['nullable', 'string', 'max:10'],
            'authorized' => ['nullable', 'string', 'max:10'],
            'worked' => ['nullable', 'string', 'max:10'],
            'convicted' => ['nullable', 'string', 'max:10'],
            'indictment' => ['nullable', 'string', 'max:10'],
            'signature' => ['nullable', 'string', 'max:255'],
            'datedisclamer' => ['nullable', 'date'],
            'recaptcha_token' => ['nullable', 'string'],
            'fileid' => ['nullable', 'array'],
            'fileid.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,doc,docx', 'max:5120'],
        ]);

        if (! $this->passesRecaptcha($request)) {
            return response()->json(['success' => false, 'message' => 'reCAPTCHA verification failed. Please try again.'], 422);
        }

        $information = DB::transaction(function () use ($request, $validated): Information {
            $inf = new Information();
            foreach (['lastname', 'firstname', 'mi', 'date', 'address', 'apartment', 'city', 'state', 'zipcode', 'phone', 'email', 'birthday', 'socialnumber', 'placebirth', 'appliedpay', 'whichshift', 'citizen', 'authorized', 'worked', 'when', 'convicted', 'explain1', 'indictment', 'explain2'] as $field) {
                $inf->{$field} = $request->input($field);
            }
            $inf->whichday = serialize($this->dayMap($request->input('days', [])));
            $inf->save();

            $education = new Education();
            foreach (['graduatehigh', 'hightschool', 'highfrom', 'hightto', 'graduatecollage', 'collaganame', 'collagefrom', 'collageto', 'whatmayor', 'completed', 'activecard', 'officer', 'firearm', 'holster', 'others'] as $field) {
                $education->{$field} = $request->input($field);
            }
            $education->information_id = $inf->id;
            $education->save();

            foreach ($request->input('fullname', []) as $index => $fullname) {
                if (! filled($fullname)) {
                    continue;
                }
                $reference = new Reference();
                $reference->fullname = $fullname;
                $reference->relationship = $request->input("relationship.{$index}");
                $reference->companyref = $request->input("companyref.{$index}");
                $reference->phoneref = $request->input("phoneref.{$index}");
                $reference->addressreference = $request->input("addressreference.{$index}");
                $reference->information_id = $inf->id;
                $reference->save();
            }

            foreach ($request->input('companyprev', []) as $index => $company) {
                if (! filled($company)) {
                    continue;
                }
                $employment = new EmploymentModel();
                $employment->company = $company;
                foreach (['phoneemp', 'addressempl', 'supervisor', 'jobtitle', 'starting', 'ending', 'empfrom', 'empto', 'reason'] as $field) {
                    $column = match ($field) {
                        'empfrom' => 'from',
                        'empto' => 'to',
                        default => $field,
                    };
                    $employment->{$column} = $request->input("{$field}.{$index}");
                }
                $employment->references = $request->input('references' . ($index + 1));
                $employment->information_id = $inf->id;
                $employment->save();
            }

            $military = new Military();
            $military->branch = $request->input('branch');
            $military->from = $request->input('frommilitary');
            $military->to = $request->input('tomilitary');
            $military->rank = $request->input('rank');
            $military->type = $request->input('type');
            $military->explain = $request->input('explain');
            $military->information_id = $inf->id;
            $military->save();

            $disclaimer = new Disclaimer();
            $disclaimer->signature = $validated['signature'] ?? null;
            $disclaimer->datedisclamer = $validated['datedisclamer'] ?? null;
            $disclaimer->information_id = $inf->id;
            $disclaimer->save();

            if ($request->hasFile('fileid')) {
                foreach ($request->file('fileid') as $file) {
                    $archivo = new Archivo();
                    $archivo->file = Storage::putFile('applied', $file);
                    $archivo->disclaimer_id = $disclaimer->id;
                    $archivo->save();
                }
            }

            return $inf;
        });

        $this->sendRawNotification('TAP employment application', "New applicant: {$information->firstname} {$information->lastname}\nID: {$information->id}\nEmail: {$information->email}");

        return response()->json(['success' => true, 'message' => 'Application submitted successfully.', 'data' => ['id' => $information->id]], 201);
    }

    public function form8850(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'yourname' => ['required', 'string', 'max:255'],
            'socialnumber' => ['required', 'string', 'max:50'],
            'address' => ['required', 'string', 'max:255'],
            'citystate' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:100'],
            'telephone' => ['required', 'string', 'max:50'],
            'birthday' => ['required', 'string', 'max:50'],
            'condicional' => ['nullable', 'array'],
            'recaptcha_token' => ['nullable', 'string'],
        ]);

        if (! $this->passesRecaptcha($request)) {
            return response()->json(['success' => false, 'message' => 'reCAPTCHA verification failed. Please try again.'], 422);
        }

        $form = new Form();
        foreach (['yourname', 'socialnumber', 'address', 'citystate', 'country', 'telephone', 'birthday'] as $field) {
            $form->{$field} = $validated[$field];
        }
        $form->condicional = serialize($validated['condicional'] ?? []);
        $form->save();

        $this->sendRawNotification('TAP Form 8850', "New Form 8850 submission\nID: {$form->id}\nName: {$form->yourname}");

        return response()->json(['success' => true, 'message' => 'Form 8850 submitted successfully.', 'data' => ['id' => $form->id]], 201);
    }

    private function transformCategory(Category $category): array
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'order' => $category->orden,
            'card_url' => $this->assetUrl($category->card),
            'banner_url' => $this->assetUrl($category->banner),
        ];
    }

    private function transformIndustryCard(Industry $industry): array
    {
        return [
            'id' => $industry->id,
            'name' => $industry->titulo,
            'slug' => $industry->slug,
            'card_url' => $this->assetUrl($industry->card),
            'banner_url' => $this->assetUrl($industry->banner),
            'summary' => Str::limit(strip_tags($industry->contenido ?? ''), 120, '...'),
        ];
    }

    private function transformPostCard(Post $post): array
    {
        return [
            'id' => $post->id,
            'title' => $post->titulo,
            'slug' => $post->slug,
            'summary' => Str::limit(strip_tags($post->resumen ?? ''), 100, '...'),
            'card_image_url' => $this->assetUrl($post->card),
            'banner_image_url' => $this->assetUrl($post->banner),
            'published_at' => optional($post->created_at)->toISOString(),
            'formatted_date' => optional($post->created_at)->format('M d, Y'),
        ];
    }

    private function transformCourseCard(Course $course): array
    {
        return [
            'id' => $course->id,
            'title' => $course->titulo,
            'slug' => $course->slug,
            'summary' => $course->resumen,
            'banner_url' => $this->assetUrl($course->banner),
            'price' => $course->precio !== null ? (float) $course->precio : null,
            'level' => $course->nivel,
            'chapters_count' => $course->capitulos !== null ? (int) $course->capitulos : null,
        ];
    }

    private function assetUrl(?string $path): ?string
    {
        if (! filled($path)) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        return url('/storage/' . ltrim($path, '/'));
    }

    private function ok(mixed $data): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $data]);
    }

    private function passesRecaptcha(Request $request): bool
    {
        if (! filled(config('services.recaptcha.secret')) && ! filled(env('CAPTCHA_SECRET'))) {
            return true;
        }

        $token = $request->input('recaptcha_token');
        if (! filled($token)) {
            return false;
        }

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('services.recaptcha.secret', env('CAPTCHA_SECRET')),
            'response' => $token,
        ])->object();

        return (bool) ($response->success ?? false) && (float) ($response->score ?? 1) >= 0.7;
    }

    private function dayMap(array $days): array
    {
        $result = ['sunday' => '', 'monday' => '', 'tuesday' => '', 'wednesday' => '', 'thursday' => '', 'friday' => '', 'saturday' => ''];
        $map = ['1' => 'sunday', '2' => 'monday', '3' => 'tuesday', '4' => 'wednesday', '5' => 'thursday', '6' => 'friday', '7' => 'saturday'];

        foreach ($days as $day) {
            if (isset($map[(string) $day])) {
                $result[$map[(string) $day]] = (int) $day;
            }
        }

        return $result;
    }

    private function sendRawNotification(string $subject, string $body): void
    {
        $to = env('MAIL_CONTACT');

        if (! filled($to)) {
            return;
        }

        try {
            Mail::raw($body, fn ($message) => $message->to($to)->subject($subject));
        } catch (\Throwable $exception) {
            report($exception);
        }
    }

    private function contactEmailText(Contact $contact): string
    {
        return "Name: {$contact->name}\nEmail: {$contact->email}\nPhone: {$contact->phone}\nOrigin: {$contact->origen}\n\n{$contact->message}";
    }
}

<!DOCTYPE html>
@php
    use Carbon\Carbon;
@endphp
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Interface</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 dark:bg-gray-900 min-h-screen"
x-data="{ isMobile: false }"
>
    <div class="mx-auto max-w-6xl p-3 md:p-4">
        <!-- Simple Header -->
        <div class="flex items-center justify-between pb-4">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Chat</h2>
            <a href="/" class="text-sm text-blue-500 hover:text-blue-600 dark:text-blue-400">← Home</a>
        </div>

        <div class="h-[calc(100vh-186px)] overflow-hidden sm:h-[calc(100vh-174px)]">
            <div class="flex h-full flex-col gap-6 xl:flex-row xl:gap-5">
                <!-- Chat Sidebar -->
                <div class="flex-col overflow-hidden rounded-xl border border-gray-200 bg-white xl:flex xl:w-1/4 dark:border-gray-700 dark:bg-gray-800/50">
                    <!-- Sidebar Header -->
                    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base font-medium text-gray-800 dark:text-white">Friends</h3>
                            <button @click="isMobile = !isMobile" class="p-1.5 rounded-md text-gray-500 hover:bg-gray-100 xl:hidden dark:text-gray-400 dark:hover:bg-gray-700">
                                <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M2.5 7.5H17.5M2.5 12.5H17.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="no-scrollbar flex-col overflow-auto" :class="isMobile ? 'flex fixed xl:static top-0 left-0 z-50 h-screen w-full bg-white dark:bg-gray-800 xl:relative xl:w-auto' : 'hidden xl:flex'" @click.outside="isMobile = false">
                        <!-- Mobile Header -->
                        <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3 xl:hidden dark:border-gray-700">
                            <h3 class="text-base font-medium text-gray-800 dark:text-white">Friends</h3>
                            <button @click="isMobile = false" class="p-1 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white">
                                <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M15 5L5 15M5 5L15 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <div class="flex max-h-full flex-col overflow-auto px-3">
                            <div class="custom-scrollbar max-h-full space-y-0.5 overflow-auto py-2">
                                @foreach ($friends as $friend)
                                <a href="{{ route('chat.page',$friend->friend_id==auth()->id()?$friend->user_id:$friend->friend_id) }}" class="flex items-center gap-2.5 rounded-lg px-2 py-2 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors">
                                    <div class="relative h-9 w-9 shrink-0">
                                        <div class="h-full w-full rounded-full bg-blue-500 flex items-center justify-center text-white text-xs font-medium">{{ substr($friend->getfriendname(), 0, 2) }}</div>
                                        <span class="absolute right-0 bottom-0 block h-2.5 w-2.5 rounded-full border-2 border-white bg-green-500 dark:border-gray-800"></span>
                                    </div>
                                    <span class="text-sm text-gray-700 dark:text-gray-200 truncate">{{ $friend->getfriendname() }}</span>
                                </a>
                                @endforeach
                                
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Chat Sidebar End -->

                <!-- Chat Box -->
                <div class="flex h-full flex-col overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800/50 xl:w-3/4">
                    <!-- Chat Header -->
                    <div class="flex items-center gap-3 border-b border-gray-200 px-4 py-3 dark:border-gray-700">
                        <div class="relative h-8 w-8 shrink-0">
                            <div class="h-full w-full rounded-full bg-emerald-500 flex items-center justify-center text-white text-xs font-medium">{{ substr($user['name'], 0, 2) }}</div>
                            <span class="absolute bottom-0 right-0 block h-2 w-2 rounded-full border border-white bg-green-500 dark:border-gray-800"></span>
                        </div>
                        <span class="text-sm font-medium text-gray-800 dark:text-white">{{$user['name']}}</span>
                    </div>

                    <!-- Messages Area -->
                    <div class="custom-scrollbar max-h-full flex-1 space-y-3 overflow-auto p-4"
                    x-data="{ messages:[] }"
                    x-init="
                    @foreach ($messages as $message)
                        messages.push({
                            message: '{{ $message->message }}',
                            sender: {{ $message->sender_id}},
                            time: '{{ $message->created_at->diffForHumans() }}',
                        });
                    @endforeach
                    Echo.private('messages.{{$RommId}}')
                    .listen('Message', (e) => {
                        messages.push(e)
                    })
                    ">
                        <template x-for="message in messages">
                            <div>
                                <template x-if="message.sender !== {{ auth()->id() }}">
                                    <div class="max-w-[280px]">
                                        <div class="rounded-lg rounded-tl-none bg-gray-100 px-3 py-2 dark:bg-gray-700">
                                            <p class="text-sm text-gray-800 dark:text-gray-100" x-text="message.message"></p>
                                        </div>
                                        <p class="mt-1 text-xs text-gray-400" x-text="message.time"></p>
                                    </div>
                                </template>

                                <template x-if="message.sender === {{ auth()->id() }}">
                                    <div class="ml-auto max-w-[280px] text-right">
                                        <div class="ml-auto max-w-max rounded-lg rounded-tr-none bg-blue-500 px-3 py-2">
                                            <p class="text-sm text-white" x-text="message.message"></p>
                                        </div>
                                        <p class="mt-1 text-xs text-gray-400" x-text="message.time"></p>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>

                    <!-- Message Input -->
                    <div class="border-t border-gray-200 p-3 dark:border-gray-700">
                        <form class="flex items-center gap-2" method="post" action="{{ route('chat.send') }}">
                            @csrf
                            <input type="text" name="message" id="message" placeholder="Type a message..." class="flex-1 h-9 rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-800 placeholder:text-gray-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:text-white dark:placeholder:text-gray-500">
                            <input type="hidden" name="userId" value="{{ $user['id'] }}">
                            <button type="submit" class="h-9 w-9 flex items-center justify-center rounded-lg bg-blue-500 text-white hover:bg-blue-600 transition-colors">
                                <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M4.98481 2.44399C3.11333 1.57147 1.15325 3.46979 1.96543 5.36824L3.82086 9.70527C3.90146 9.89367 3.90146 10.1069 3.82086 10.2953L1.96543 14.6323C1.15326 16.5307 3.11332 18.4291 4.98481 17.5565L16.8184 12.0395C18.5508 11.2319 18.5508 8.76865 16.8184 7.961L4.98481 2.44399ZM3.34453 4.77824C3.0738 4.14543 3.72716 3.51266 4.35099 3.80349L16.1846 9.32051C16.762 9.58973 16.762 10.4108 16.1846 10.68L4.35098 16.197C3.72716 16.4879 3.0738 15.8551 3.34453 15.2223L5.19996 10.8853C5.21944 10.8397 5.23735 10.7937 5.2537 10.7473L9.11784 10.7473C9.53206 10.7473 9.86784 10.4115 9.86784 9.99726C9.86784 9.58304 9.53206 9.24726 9.11784 9.24726L5.25157 9.24726C5.2358 9.20287 5.2186 9.15885 5.19996 9.11528L3.34453 4.77824Z" fill="white"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

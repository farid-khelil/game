<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .notification {
            position: fixed;
            right: 1rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            display: block;
            animation: slideIn 0.5s ease-out;
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            min-width: 300px;
            max-width: 400px;
            z-index: 40;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(100%);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .notification-icon {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .notification-button {
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
        }

        .notification-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .button:hover {
            transform: scale(1.05);
            transition: transform 0.2s ease-in-out;
        }

        .error-notification {
            position: fixed;
            right: 1rem;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            padding: 1rem 1.25rem;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(239, 68, 68, 0.3);
            display: block;
            animation: slideIn 0.4s ease-out;
            border: 1px solid rgba(255, 255, 255, 0.1);
            min-width: 280px;
            max-width: 360px;
            z-index: 100;
        }

        .success-notification {
            position: fixed;
            right: 1rem;
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            color: white;
            padding: 1rem 1.25rem;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(34, 197, 94, 0.3);
            display: block;
            animation: slideIn 0.4s ease-out;
            border: 1px solid rgba(255, 255, 255, 0.1);
            min-width: 280px;
            max-width: 360px;
            z-index: 100;
        }
    </style>
</head>

<body class="bg-gray-900 text-white" id="element"
x-data="{
    users : [],
    GameInvites: [] ,
    friendrequests: [],
    errors: [],
    successes: [],
    showInfoPopup: false,
    showError(message) {
        const id = Date.now();
        this.errors.push({ id, message });
        setTimeout(() => {
            this.errors = this.errors.filter(e => e.id !== id);
        }, 5000);
    },
    showSuccess(message) {
        const id = Date.now();
        this.successes.push({ id, message });
        setTimeout(() => {
            this.successes = this.successes.filter(s => s.id !== id);
        }, 4000);
    }
    }"
    x-init="
    @if(session('error'))
        showError('{{ session('error') }}');
    @endif
    @if(session('success'))
        showSuccess('{{ session('success') }}');
    @endif
    @foreach ($requests as $request)
        friendrequests.push({
            id: {{ $request->id }},
            senderName: '{{ $request->getusername() }}'
        });
        
    @endforeach
    console.log(friendrequests);
    if (typeof Echo !== 'undefined') {
        Echo.join('onlineusers.1')
            .here((e) => {
            users = e.map(u => u.id);
            }).joining((e) => {
               users.push(e.id);
            })
            .leaving((e) => {
               users = users.filter(u => u !== e.id);
            })
        Echo.private('notification.' + {{ auth()->id() }})
            .listen('invite', (e) => {
                GameInvites.push(e);

            });
        Echo.private('notification.' + {{ auth()->id() }})
            .listen('FriendRequestSent', (e) => {
                friendrequests.push(e);
            });
    }

    "
>
<!-- Info Button (Fixed) -->
<button @click="showInfoPopup = true" class="fixed bottom-6 left-6 w-12 h-12 bg-purple-600 hover:bg-purple-700 rounded-full flex items-center justify-center shadow-lg hover:shadow-xl transition-all z-50" title="How to Play">
    <img src="https://img.icons8.com/?id=17077&format=png&size=32" alt="Info" class="w-6 h-6 invert">
</button>

<!-- Info Popup Modal -->
<div x-show="showInfoPopup" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" @click.self="showInfoPopup = false">
    <div x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="bg-gray-800 rounded-2xl max-w-2xl w-full max-h-[85vh] overflow-hidden shadow-2xl border border-gray-700" @click.stop>
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-700 bg-gradient-to-r from-purple-600/20 to-blue-600/20">
            <div class="flex items-center gap-3">
                <img src="https://img.icons8.com/?id=7314&format=png&size=32" alt="Game" class="w-8 h-8 invert">
                <h2 class="text-xl font-bold text-white">How to Play</h2>
            </div>
            <button @click="showInfoPopup = false" class="p-2 hover:bg-white/10 rounded-lg transition-colors">
                <img src="https://img.icons8.com/?id=46&format=png&size=24" alt="Close" class="w-5 h-5 invert opacity-70 hover:opacity-100">
            </button>
        </div>
        
        <!-- Content -->
        <div class="p-6 overflow-y-auto max-h-[calc(85vh-80px)] space-y-6">
            <!-- Title Section -->
            <div class="text-center pb-4 border-b border-gray-700/50">
                <h3 class="text-2xl font-bold text-purple-400">Ultimate Tic-Tac-Toe</h3>
                <p class="text-gray-400 mt-1">A strategic boardgame for 2 players</p>
            </div>

            <!-- Goal Section -->
            <div class="bg-gray-700/30 rounded-xl p-4">
                <div class="flex items-center gap-3 mb-3">
                    <img src="https://img.icons8.com/?id=20884&format=png&size=32" alt="Goal" class="w-6 h-6 invert">
                    <h4 class="text-lg font-semibold text-white">Goal</h4>
                </div>
                <p class="text-gray-300 text-sm leading-relaxed">
                    Win <span class="text-purple-400 font-semibold">three small grids in a row</span> (horizontally, vertically, or diagonally) on the large board to win the game!
                </p>
            </div>

            <!-- Rules Section -->
            <div class="bg-gray-700/30 rounded-xl p-4">
                <div class="flex items-center gap-3 mb-3">
                    <img src="https://img.icons8.com/?id=6440&format=png&size=32" alt="Rules" class="w-6 h-6 invert">
                    <h4 class="text-lg font-semibold text-white">Rules</h4>
                </div>
                <ul class="space-y-3 text-gray-300 text-sm">
                    <li class="flex gap-3">
                        <span class="text-purple-400 font-bold">1.</span>
                        <span>The board contains <span class="text-blue-400">nine smaller Tic-Tac-Toe boards</span> arranged in a 3x3 pattern.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="text-purple-400 font-bold">2.</span>
                        <span>Your move determines where your opponent plays next. If you play in the <span class="text-yellow-400">top-right cell</span> of any small grid, your opponent must play in the <span class="text-yellow-400">top-right small grid</span>.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="text-purple-400 font-bold">3.</span>
                        <span>Win a small grid by getting three in a row within it, just like regular Tic-Tac-Toe.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="text-purple-400 font-bold">4.</span>
                        <span>If you're sent to a grid that's already won or full, you can <span class="text-green-400">play anywhere</span> on the board!</span>
                    </li>
                </ul>
            </div>

            <!-- Tips Section -->
            <div class="bg-purple-500/10 border border-purple-500/30 rounded-xl p-4">
                <h4 class="text-lg font-semibold text-purple-400 mb-2">💡 Pro Tip</h4>
                <p class="text-gray-300 text-sm">
                    Think ahead! Every move you make determines where your opponent can play. Try to send them to grids where they have fewer options!
                </p>
            </div>

            <!-- Credit -->
            <p class="text-center text-gray-500 text-xs pt-2">
                Learn more at <a href="https://bejofo.com/ttt" target="_blank" class="text-purple-400 hover:underline">bejofo.com/ttt</a>
            </p>
        </div>
    </div>
</div>

<div class="flex p-7">
    <!-- Error Notifications -->
    <template x-for="(error, index) in errors" :key="error.id">
        <div class="error-notification" :style="'bottom: ' + (1 + index * 5) + 'rem;'">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-lg">⚠️</div>
                <div class="flex-1">
                    <p class="text-sm font-medium" x-text="error.message"></p>
                </div>
                <button @click="errors = errors.filter(e => e.id !== error.id)" class="text-white/70 hover:text-white text-lg">&times;</button>
            </div>
        </div>
    </template>

    <!-- Success Notifications -->
    <template x-for="(success, index) in successes" :key="success.id">
        <div class="success-notification" :style="'bottom: ' + (1 + (errors.length + index) * 5) + 'rem;'">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-lg">✓</div>
                <div class="flex-1">
                    <p class="text-sm font-medium" x-text="success.message"></p>
                </div>
                <button @click="successes = successes.filter(s => s.id !== success.id)" class="text-white/70 hover:text-white text-lg">&times;</button>
            </div>
        </div>
    </template>

    <template x-for="(invite, index) in GameInvites" :key="invite.gameId">
    <div class="notification" :style="'top: ' + (1 + index * 10) + 'rem;'">
        <div class="flex items-start space-x-3">
            <div class="notification-icon">
                🎮
            </div>
            <div class="flex-1">
                <h4 class="font-semibold text-lg mb-1" x-text="invite.user1?.name"></h4>
                <p class="text-sm opacity-90 mb-3">Inivited you to a game</p>
                <div class="flex space-x-2">
                    <a x-bind:href="'/game/'+invite.gameId"><button class="notification-button bg-white text-purple-700 hover:bg-gray-100">Join</button></a>
                    <button class="notification-button bg-transparent border border-white text-white hover:bg-red-700 hover:text-white" @click="GameInvites.splice(index, 1)">Dismiss</button>
                </div>
            </div>
        </div>
    </div>
    </template>
    <template x-for="(friendrequest, index) in friendrequests" :key="friendrequest.id + '-' + index">
        <div class="notification" :style="'top: ' + (1 + (GameInvites.length + index) * 10) + 'rem;'">
            <div class="flex items-start space-x-3">
                <div class="notification-icon">
                    👤
                </div>
                <div class="flex-1">
                    <h4 class="font-semibold text-lg mb-1" x-text="friendrequest.senderName"></h4>
                    <p class="text-sm opacity-90 mb-3">sened you a friends request</p>
                    <div class="flex space-x-2">
                        <button @click="accespt(friendrequest.id, friendrequests)" class="notification-button bg-white text-purple-700 hover:bg-gray-100">accepte</button>
                        <button @click="dicline(friendrequest.id,friendrequests)" class="notification-button bg-transparent border border-white text-white hover:bg-red-700 hover:text-white">decline</button>
                    </div>
                </div>
            </div>
        </div>
    </template>
        
    <div class="max-w-3xl mx-auto mt-12 bg-gray-800 p-6 rounded-lg shadow-lg"
    >
        <h2 class="text-center text-2xl font-bold mb-6">Online Users</h2>
        <ul class="space-y-4">
            @foreach ($users as $user)
        <li class="flex justify-between items-center bg-gray-700 p-4 rounded-md hover:bg-gray-600 transition-colors">
            
            <div class="flex items-center">
                <template x-if="!users.includes({{ $user->id }})">
                    <span class="w-3 h-3 rounded-full bg-red-500 mr-2"></span>
                </template>
                <template x-if="users.includes({{ $user->id }})">
                    <span class="w-3 h-3 rounded-full bg-green-500 mr-2"></span>
                </template>
                {{ $user->name }}
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('chat.page',$user->id) }}">
                <button class="button bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600">Message</button>
                </a>
                <button onclick="sendrequest({{ $user->id }})" class="button bg-green-500 text-white px-3 py-1 rounded-md hover:bg-green-600">Invite as Friend</button>
                <a href="{{ route('game.create',[auth()->id(),$user->id]) }}">
                    <button class="button bg-yellow-500 text-black px-3 py-1 rounded-md hover:bg-yellow-600">Play Game</button>
                </a>
            </div>
        </li>
            @endforeach
        </ul>
    </div>

    <!-- Old Games Section -->
    @if(count($games) > 0)
    <div class="w-full max-w-md ml-6 relative z-10">
        <div class="bg-gray-800/50 backdrop-blur rounded-xl border border-gray-700/50 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-700/50">
                <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                    <span class="text-xl">🎮</span> Continue Playing
                </h3>
                <p class="text-sm text-gray-400 mt-1">{{ count($games) }} active game{{ count($games) > 1 ? 's' : '' }}</p>
            </div>
            <div class="divide-y divide-gray-700/30">
                @foreach ($games as $game)
                <a href="{{ route('game',$game->id) }}" class="group flex items-center justify-between px-5 py-4 hover:bg-gray-700/30 transition-all">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-purple-500/20 flex items-center justify-center text-purple-400 font-semibold text-sm">
                            {{ strtoupper(substr($game->getOpponentName()['name'], 0, 2)) }}
                        </div>
                        <div>
                            <p class="font-medium text-white group-hover:text-purple-300 transition-colors">{{ $game->getOpponentName()['name'] }}</p>
                            <p class="text-xs text-gray-500">Tap to continue</p>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-gray-500 group-hover:text-purple-400 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
<script>
function getAlpineData() {
    return document.getElementById('element')._x_dataStack[0];
}

function accespt(id, friendrequests) {
    const alpine = getAlpineData();
    const index = friendrequests.findIndex(request => request.id === id);
    if (index !== -1) {
        friendrequests.splice(index, 1);
    }    
    axios.post("{{ route('friends.accept') }}", {
        id: id
    }).then(response => {
        alpine.showSuccess('Friend request accepted!');
    }).catch(error => {
        alpine.showError(error.response?.data?.error || 'Failed to accept friend request');
    });
}
function dicline(id,friendrequests) {
    const alpine = getAlpineData();
    const index = friendrequests.findIndex(request => request.id === id);
    if (index !== -1) {
        friendrequests.splice(index, 1);
    } 
    axios.post("{{ route('friends.decline') }}", {
        id: id
    }).then(response => {
        alpine.showSuccess('Friend request declined');
    }).catch(error => {
        alpine.showError(error.response?.data?.error || 'Failed to decline friend request');
    });
}
function sendrequest(id) {
    const alpine = getAlpineData();
    axios.post("{{ route('friends.request') }}", {
        friend_id: id
    }).then(response => {
        alpine.showSuccess('Friend request sent!');
    }).catch(error => {
        alpine.showError(error.response?.data?.error || 'Failed to send friend request');
    }); 
}
</script>
</html>
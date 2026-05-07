<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">Email notifikacije</h2>
        <p class="mt-1 text-sm text-gray-600">
            Ukljucite ili iskljucite slanje email upozorenja kada predjete prag budzeta.
        </p>
    </header>

    <form method="POST" action="{{ route('profile.notifications') }}" class="mt-6 space-y-4">
        @csrf
        @method('PATCH')

        <label class="inline-flex items-center gap-2">
            <input type="checkbox" name="email_notifications" value="1" @checked(auth()->user()->email_notifications)
                   class="rounded border-gray-300 text-app-accent focus:ring-app-accent email-notifications-toggle">
            <span class="text-sm">Primam email notifikacije za budzete</span>
        </label>

        <div>
            <button type="submit" class="px-4 py-2 bg-app-accent hover:bg-app-accent-hov text-white rounded-lg text-sm font-medium">Sacuvaj</button>
        </div>
    </form>
</section>

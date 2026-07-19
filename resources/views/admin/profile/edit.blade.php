@extends('layouts.admin', ['title' => 'SalesPro', 'pageTitle' => 'Profile'])

@section('content')
    <section class="content">
        <div class="page-header">
            <div>
                <h2>Profile</h2>
                <p>Update owner account details, password and profile photo.</p>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <section class="panel profile-panel">
            <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="profile-form-grid">
                    <div class="profile-photo-card">
                        <div class="profile-photo-preview">
                            @if ($user->profilePhotoUrl())
                                <img src="{{ $user->profilePhotoUrl() }}" alt="{{ $user->name }}">
                            @else
                                <span>{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            @endif
                        </div>
                        <strong>{{ $user->name }}</strong>
                        <span>{{ $user->email }}</span>

                        <label class="secondary-button profile-upload" for="profile_photo">Upload Photo</label>
                        <input id="profile_photo" class="visually-hidden" type="file" name="profile_photo" accept="image/*">
                        @error('profile_photo')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <div class="form-grid">
                            <div class="field-group">
                                <label for="name">Owner Name</label>
                                <input id="name" class="field-control" type="text" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <span class="error-text">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="field-group">
                                <label for="designation">Designation</label>
                                <input id="designation" class="field-control" type="text" name="designation" value="{{ old('designation', $user->designation ?? 'Owner Admin') }}" required>
                                @error('designation')
                                    <span class="error-text">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="field-group">
                                <label for="email">Email</label>
                                <input id="email" class="field-control" type="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <span class="error-text">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="field-group">
                                <label for="password">New Password</label>
                                <div class="password-field">
                                    <input id="password" class="field-control" type="password" name="password" autocomplete="new-password">
                                    <button class="password-toggle" type="button" aria-label="Show password" data-password-toggle="password">
                                        <svg class="eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                        <svg class="eye-closed hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path d="m3 3 18 18"/>
                                            <path d="M10.6 10.6A2 2 0 0 0 12 14a2 2 0 0 0 1.4-.6"/>
                                            <path d="M9.9 4.3A9.4 9.4 0 0 1 12 4c6.5 0 10 8 10 8a17.4 17.4 0 0 1-3.1 4.2"/>
                                            <path d="M6.6 6.6A17.8 17.8 0 0 0 2 12s3.5 8 10 8a9.8 9.8 0 0 0 4.2-.9"/>
                                        </svg>
                                    </button>
                                </div>
                                @error('password')
                                    <span class="error-text">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="field-group">
                                <label for="password_confirmation">Confirm Password</label>
                                <div class="password-field">
                                    <input id="password_confirmation" class="field-control" type="password" name="password_confirmation" autocomplete="new-password">
                                    <button class="password-toggle" type="button" aria-label="Show password confirmation" data-password-toggle="password_confirmation">
                                        <svg class="eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                        <svg class="eye-closed hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path d="m3 3 18 18"/>
                                            <path d="M10.6 10.6A2 2 0 0 0 12 14a2 2 0 0 0 1.4-.6"/>
                                            <path d="M9.9 4.3A9.4 9.4 0 0 1 12 4c6.5 0 10 8 10 8a17.4 17.4 0 0 1-3.1 4.2"/>
                                            <path d="M6.6 6.6A17.8 17.8 0 0 0 2 12s3.5 8 10 8a9.8 9.8 0 0 0 4.2-.9"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <a class="secondary-button" href="{{ route('admin.dashboard') }}">Cancel</a>
                            <button class="primary-button" type="submit">Update Profile</button>
                        </div>
                    </div>
                </div>
            </form>
        </section>
    </section>

    <script>
        document.querySelectorAll('[data-password-toggle]').forEach((button) => {
            button.addEventListener('click', () => {
                const input = document.getElementById(button.dataset.passwordToggle);
                const shouldShow = input.type === 'password';

                input.type = shouldShow ? 'text' : 'password';
                button.querySelector('.eye-open').classList.toggle('hidden', shouldShow);
                button.querySelector('.eye-closed').classList.toggle('hidden', ! shouldShow);
            });
        });
    </script>
@endsection

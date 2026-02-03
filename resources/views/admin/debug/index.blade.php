@extends('layouts.admin')

@section('title', 'Debug Report')
@section('page-title', 'Debug Report')

@section('content')
    <div class="card" style="max-width: 860px;">
        <div class="card-header">
            <div>
                <h2 style="margin-bottom: 4px;">Send Debug Info</h2>
                <p style="color: var(--gray-500); font-size: 14px;">
                    Submit a report so we can investigate and fix issues faster.
                </p>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('debug.store') }}" class="form">
                @csrf

                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="form-label" for="subject">Subject</label>
                    <input
                        id="subject"
                        name="subject"
                        type="text"
                        class="form-input @error('subject') error @enderror"
                        value="{{ old('subject') }}"
                        required
                        placeholder="Short summary of the issue"
                    />
                    @error('subject')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="form-label" for="contact_email">Your Email (optional)</label>
                    <input
                        id="contact_email"
                        name="contact_email"
                        type="email"
                        class="form-input @error('contact_email') error @enderror"
                        value="{{ old('contact_email') }}"
                        placeholder="you@example.com"
                    />
                    @error('contact_email')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label" for="message">Details</label>
                    <textarea
                        id="message"
                        name="message"
                        class="form-input @error('message') error @enderror"
                        rows="6"
                        required
                        placeholder="What happened? Steps to reproduce?"
                    >{{ old('message') }}</textarea>
                    @error('message')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div style="display: flex; gap: 12px; align-items: center;">
                    <button type="submit" class="btn btn-primary">Submit Debug Report</button>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
                </div>
            </form>
        </div>
    </div>
@endsection

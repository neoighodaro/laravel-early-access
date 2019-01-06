@extends('early-access::layouts.main')

@section('content')
    <div class="container flex flex-col md:flex-row items-center justify-between">
        <div class="md:pr-5 lg:pr-10 md:w-2/5">
            <div class="text-center md:text-left">
                <h1 class="text-3xl lg:text-5xl text-indigo-darker font-logo font-medium uppercase leading-normal">
                    @lang('early-access::messages.welcome', ['name' => config('app.name')])
                </h1>
                <h4 class="text-base font-light leading-normal grey-soft">
                    @lang('early-access::messages.description', ['name' => config('app.name')])
                </h4>
            </div>
            <div class="mt-10">
                @if (session('success'))
                    <div class="mb-2 p-3 rounded-lg bg-green-lightest text-sm text-green-dark font-medium"
                         id="success-msg">
                        @lang('early-access::messages.alerts.success')
                    </div>
                @endif

                @if ($errors->has('email') or session('error'))
                    <div class="mb-2 p-3 rounded-lg bg-red-lightest text-sm text-grey-darker font-medium"
                         id="error-msg">
                        {{ $errors->has('email') ? $errors->first('email') : trans('early-access::messages.alerts.error') }}
                    </div>
                @endif

                <form action="{{ route('early-access.subscribe') }}" method="post">
                    @csrf
                    <div class="flex flex-col lg:flex-row font-light">
                        <input required
                               type="email"
                               name="email"
                               value="{{ old('email') }}"
                               placeholder="@lang('early-access::common.email_address')"
                               class="p-4 w-full lg:w-3/5 rounded-lg lg:rounded-r-none bg-grey-lighter focus:bg-grey-light focus:outline-none"
                        />
                        <button type="submit"
                                class="mt-2 lg:mt-0 p-4 lg:px-0 w-full lg:w-2/5 rounded-lg lg:rounded-l-none bg-indigo-dark shadow-lg
                                       lg:shadow-none text-sm text-white uppercase focus:outline-none"
                        >
                            @lang('early-access::messages.get_early_access')<sup>†</sup>
                        </button>
                    </div>
                </form>
                <div class="mt-10">
                    <span class="text-xs text-grey font-light tracking-wide">
                        <sup>†</sup>@lang('early-access::messages.no_spam')
                    </span>
                </div>
            </div>
        </div>
        <div class="mt-16 md:mt-0 md:pl-5 lg:pl-10 md:w-3/5 text-center md:text-left">
            <img src="{{ asset('vendor/early-access/svg/placeholder.svg') }}" class="block"/>
        </div>
    </div>
@endsection

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>{{ $booking->title }}</title>

  <link href="/css/style.css" rel="stylesheet">

</head>

<body class="">
  <section class="h-full gradient-form bg-gray-200 md:h-screen">
    <div class="container py-12 px-6 h-full">
      <div class="flex justify-center items-center flex-wrap h-full g-6 text-gray-800">
        <div class="xl:w-10/12">
          <div class="block bg-white shadow-lg rounded-lg">
            <div class="lg:flex lg:flex-wrap g-0">
              <div class="lg:w-6/12 px-4 md:px-0">
                <div class="md:p-12 md:mx-6">
                  <div class="text-center">
                    <img class="mx-auto w-48" src="{{ $booking->photo }}" alt="logo" />
                    <h4 class="text-xl font-semibold mt-1 mb-12 pb-1">You've been invited by
                      {{ $booking->user->firstname }} {{ $booking->user->lastname }}</h4>
                  </div>
                  <form action="{{ route('accept.invite', $booking->uuid) }}" method="POST">
                    <p class="mb-4">Please fill in the form below</p>
                    @if (session()->has('message'))
                      <div class="bg-green-100 rounded-lg relative py-3 px-6 text-base text-green-700 mb-3">
                        <a onclick="closeParent(this)" href="#" class="close" data-dismiss="alert"
                          aria-label="close">×</a>

                        {{ session()->get('message') }}
                      </div>
                    @endif
                    @if (session()->has('error'))
                      <div class="bg-red-100 rounded-lg relative py-3 px-6 text-base text-red-700 mb-3" role="alert">
                        <a onclick="closeParent(this)" href="#" class="close" data-dismiss="alert"
                          aria-label="close">×</a>

                        {{ session()->get('error') }}
                      </div>
                    @endif
                    {{ csrf_field() }}
                    <div class="mb-4">
                      <input type="text"
                        class="form-control block w-full px-3 py-1.5 text-base font-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none"
                        id="exampleFormControlInput1" name="firstname" placeholder="Firstname" required />
                      @error('firstname')
                        <span class="flex items-center font-medium tracking-wide text-red-500 text-xs mt-1 ml-1">
                          {{-- <strong>{{ $errors->first('email') }}</strong> --}}
                          <strong>{{ $message }}</strong>
                        </span>
                      @enderror
                    </div>
                    <div class="mb-4">
                      <input type="text"
                        class="form-control block w-full px-3 py-1.5 text-base font-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none"
                        id="exampleFormControlInput1" name="lastname" placeholder="Lastname" required />
                      @error('lastname')
                        <span class="flex items-center font-medium tracking-wide text-red-500 text-xs mt-1 ml-1">
                          {{-- <strong>{{ $errors->first('email') }}</strong> --}}
                          <strong>{{ $message }}</strong>
                        </span>
                      @enderror
                    </div>
                    <div class="mb-4">
                      <input type="email"
                        class="form-control block w-full px-3 py-1.5 text-base font-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none"
                        id="exampleFormControlInput1" name="email" placeholder="Email" required />
                      @error('email')
                        <span class="flex items-center font-medium tracking-wide text-red-500 text-xs mt-1 ml-1">
                          {{-- <strong>{{ $errors->first('email') }}</strong> --}}
                          <strong>{{ $message }}</strong>
                        </span>
                      @enderror
                    </div>
                    <div class="text-center pt-1 mb-12 pb-1">
                      <button
                        class="inline-block px-6 py-2.5 bg-customBlue-200 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-blue-700 hover:shadow-lg focus:shadow-lg focus:outline-none focus:ring-0 active:shadow-lg transition duration-150 ease-in-out w-full mb-3"
                        type="submit">
                        Accept invitation
                      </button>
                    </div>

                  </form>
                </div>
              </div>
              <div
                class="lg:w-6/12 flex items-center lg:rounded-r-lg rounded-b-lg lg:rounded-bl-none bg-customBlue-200">
                <div class="text-white px-4 py-6 md:p-12 md:mx-6">
                  <h4 class="text-xl font-semibold mb-6">Booking: {{ $booking->title }}</h4>
                  <p class="text-sm">
                    {{ $booking->description }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <script>
    function closeParent(el) {
      el.parentNode.parentNode.removeChild(el.parentNode);
    }
  </script>

</body>

</html>

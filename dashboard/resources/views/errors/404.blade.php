@extends('../layout/landing')
@section('title', '404 Not Found')

@section('style')
<style>
  
  body {
    background-image: url("../img/slide-bg.png");
    background-repeat: no-repeat;
    background-position: center;
    background-size: cover;
    min-width: 100vw;
    min-height: 100vh;
  }

  .error-page {
    margin-top: 100px;
    color: #fff;
    font-weight: lighter;
  }

  .error > img {
    margin-bottom: auto;
    margin-top: auto;
    display: inline;
  }

  .error > h1 {
    font-weight: 300;
    font-size: 50px;
  }

  .error .des {
    font-size: 20px;
  }

  @media (max-width: 768px) {
    .error-page {
      margin-top: 0;
    }
    .error-page > h1 {
      font-size: 26px;
    }
    .error-page .des {
      font-size: 14px;
    }
  }
</style>
@endsection

@section('content')
<section class="error-page">
  <div class="row">
    <div class="col-sm-6 col-sm-offset-3">
      <div class="text-center error">
        <img src="../img/404.png" alt="404" class="img-responsive">
        <h1>Sorry, page not found</h1>
        <p class="des">Perhaps you clicked an outdated link, or we had removed the file</p>
        <p class="text-uppercase" style="font-size: 20px; margin:20px;">back to</p>

        <ul class="nav mnavbar mnavbar-center">
          <li class="bordered"><a href="../index.html">Homepage</a></li>
          <li class="bordered"><a href="features.html">Features</a></li>
          <li class="bordered"><a href="contact.html">Contact us</a></li>
        </ul>

      </div>
    </div>
  </div>
</section>
@endsection
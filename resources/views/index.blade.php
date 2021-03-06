@extends('layouts.app')

@section('content')
    <!-- banner-section start -->
   
    <section class="banner-section">
        <div class="banner-content-area">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <!-- <div class="banner-content text-center">
                            <h1 class="banner-title wow fadeIn" data-wow-duration="0.5s" data-wow-delay="1s">
                                Advertisement With Bet+</h1>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
        <!-- <div class="banner-bottom">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-4 col-md-4">
                        <div class="thumb">
                            <img src="{{asset('assets/images/banner-bottom.jpg') }}" alt="banner-bottom-image">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <div class="promo-video-part d-flex">
                            <a href="https://www.youtube.com/embed/aFYlAzQHnY4" data-rel="lightcase:myCollection"
                                class="round-video-btn"><i class="fa fa-play"></i></a>
                            <div class="content">
                                <span>Promo Video</span>
                                <p>Watch our intro</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5 col-md-4">
                        <div class="btn-area">
                            <a href="#" class="cmn-btn btn-lg">beting now</a>
                            <a href="#" class="cmn-btn border-btn btn-lg">explore more</a>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->
    </section>
    <!-- banner-section end -->

    <!-- feature-section start -->
    <section class="feature-section section-padding">
        <div class="container">
            <div class="row justify-content-center" style="display:none">
                <div class="col-lg-6">
                    <div class="section-header text-center">
                        <h2 class="section-title">Butlar Feature</h2>
                        <p>On insensible possession oh particular attachment at excellence in. The books arose but miles
                            happy she.</p>
                    </div>
                </div>
            </div>
            <div class="row margin-bottom-not-30">
                <div class="col-lg-4 col-sm-6">
                    <div class="feature-item text-center">
                        <div class="icon">
                            <div class="rotate-icon"><i class="flaticon-shield"></i></div>
                        </div>
                        <h3 class="title">{{__('messages.safe_and_secure')}}</h3>
                        <p>{{__('messages.safe_and_secure_text')}}</p>
                    </div>
                </div><!-- feature-item end -->
                <div class="col-lg-4 col-sm-6">
                    <div class="feature-item text-center">
                        <div class="icon">
                            <div class="rotate-icon"><i class="flaticon-presentation"></i></div>
                        </div>
                        <h3 class="title">{{__('messages.training_mode')}}</h3>
                        <p>{{__('messages.training_mode_text')}}</p>
                    </div>
                </div><!-- feature-item end -->
                <div class="col-lg-4 col-sm-6">
                    <div class="feature-item text-center">
                        <div class="icon">
                            <div class="rotate-icon"><i class="flaticon-computer"></i></div>
                        </div>
                        <h3 class="title">{{__('messages.full_market_depth')}}</h3>
                        <p>{{__('messages.full_market_depth_text')}}</p>
                    </div>
                </div><!-- feature-item end -->
                <div class="col-lg-4 col-sm-6">
                    <div class="feature-item text-center">
                        <div class="icon">
                            <div class="rotate-icon"><i class="flaticon-book"></i></div>
                        </div>
                        <h3 class="title">{{__('messages.sports_book')}}</h3>
                        <p>{{__('messages.sports_book_text')}}</p>
                    </div>
                </div><!-- feature-item end -->
                <div class="col-lg-4 col-sm-6">
                    <div class="feature-item text-center">
                        <div class="icon">
                            <div class="rotate-icon"><i
                                    class="flaticon-one-finger-tap-gesture-of-outlined-hand-symbol"></i></div>
                        </div>
                        <h3 class="title">{{__('messages.just_a_click')}}</h3>
                        <p>{{__('messages.just_a_click_text')}}</p>
                    </div>
                </div><!-- feature-item end -->
                <div class="col-lg-4 col-sm-6">
                    <div class="feature-item text-center">
                        <div class="icon">
                            <div class="rotate-icon"><i class="flaticon-discuss-issue"></i></div>
                        </div>
                        <h3 class="title">{{__('messages.24x_live_chating')}}</h3>
                        <p>{{__('messages.24x_live_chating_text')}}</p>
                    </div>
                </div><!-- feature-item end -->
            </div>
        </div>
    </section>
    <!-- feature-section end -->

    <!-- play-section start -->
    <section class="play-section section-padding section-bg" style="display:none">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="section-header text-center">
                        <h2 class="section-title">Bet & Playing Now</h2>
                        <p>On insensible possession oh particular attachment at excellence in. The books arose but miles
                            happy she.</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="online-play-tab-part">
                        <ul class="nav justify-content-around" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="all-tab" data-toggle="tab" href="#all" role="tab"
                                    aria-controls="all" aria-selected="true">
                                    <i class="flaticon-trophy"></i>
                                    <span>All sports</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="football-tab" data-toggle="tab" href="#football" role="tab"
                                    aria-controls="football" aria-selected="false">
                                    <i class="flaticon-football"></i>
                                    <span>football</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="cricket-tab" data-toggle="tab" href="#cricket" role="tab"
                                    aria-controls="cricket" aria-selected="false">
                                    <i class="flaticon-cricket"></i>
                                    <span>cricket</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tennis-tab" data-toggle="tab" href="#tennis" role="tab"
                                    aria-controls="tennis" aria-selected="false">
                                    <i class="flaticon-tennis-racket"></i>
                                    <span>tennis</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="bascketball-tab" data-toggle="tab" href="#bascketball"
                                    role="tab" aria-controls="bascketball" aria-selected="false">
                                    <i class="flaticon-basketball"></i>
                                    <span>bascketball</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="baseball-tab" data-toggle="tab" href="#baseball" role="tab"
                                    aria-controls="baseball" aria-selected="false">
                                    <i class="flaticon-softball"></i>
                                    <span>baseball</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="more-tab" data-toggle="tab" href="#more" role="tab"
                                    aria-controls="more" aria-selected="false">
                                    <i class="flaticon-menu"></i>
                                    <span>more sports</span>
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                                <div class="play-table-part">
                                    <div class="play-table">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th class="team-name">name of the team</th>
                                                    <th class="goal">goal</th>
                                                    <th class="choice-team">bet your choose team</th>
                                                    <th class="bet-num">total bet</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <div class="team-name-part">
                                                            <i class="fa fa-futbol-o"></i>
                                                            <div class="content">
                                                                <span class="name">Barcelona</span>
                                                                <span class="name">Real madrid</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="score">
                                                            <span class="first-team-score">2</span>
                                                            <span class="second-team-score">2</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="choice-team-part">
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-down"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="bet-count-num">+19<i
                                                                class="fa fa-angle-right"></i></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="team-name-part">
                                                            <i class="fa fa-futbol-o"></i>
                                                            <div class="content">
                                                                <span class="name">Barcelona</span>
                                                                <span class="name">Real madrid</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="score">
                                                            <span class="first-team-score">2</span>
                                                            <span class="second-team-score">2</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="choice-team-part">
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-down"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="bet-count-num">+19<i
                                                                class="fa fa-angle-right"></i></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="team-name-part">
                                                            <i class="fa fa-futbol-o"></i>
                                                            <div class="content">
                                                                <span class="name">Barcelona</span>
                                                                <span class="name">Real madrid</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="score">
                                                            <span class="first-team-score">2</span>
                                                            <span class="second-team-score">2</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="choice-team-part">
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-down"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="bet-count-num">+19<i
                                                                class="fa fa-angle-right"></i></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="team-name-part">
                                                            <i class="fa fa-futbol-o"></i>
                                                            <div class="content">
                                                                <span class="name">Barcelona</span>
                                                                <span class="name">Real madrid</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="score">
                                                            <span class="first-team-score">2</span>
                                                            <span class="second-team-score">2</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="choice-team-part">
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-down"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="bet-count-num">+19<i
                                                                class="fa fa-angle-right"></i></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="team-name-part">
                                                            <i class="fa fa-futbol-o"></i>
                                                            <div class="content">
                                                                <span class="name">Barcelona</span>
                                                                <span class="name">Real madrid</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="score">
                                                            <span class="first-team-score">2</span>
                                                            <span class="second-team-score">2</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="choice-team-part">
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-down"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="bet-count-num">+19<i
                                                                class="fa fa-angle-right"></i></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="team-name-part">
                                                            <i class="fa fa-futbol-o"></i>
                                                            <div class="content">
                                                                <span class="name">Barcelona</span>
                                                                <span class="name">Real madrid</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="score">
                                                            <span class="first-team-score">2</span>
                                                            <span class="second-team-score">2</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="choice-team-part">
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-down"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="bet-count-num">+19<i
                                                                class="fa fa-angle-right"></i></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="team-name-part">
                                                            <i class="fa fa-futbol-o"></i>
                                                            <div class="content">
                                                                <span class="name">Barcelona</span>
                                                                <span class="name">Real madrid</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="score">
                                                            <span class="first-team-score">2</span>
                                                            <span class="second-team-score">2</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="choice-team-part">
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-down"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="bet-count-num">+19<i
                                                                class="fa fa-angle-right"></i></span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="football" role="tabpanel" aria-labelledby="football-tab">
                                <div class="play-table-part">
                                    <div class="play-table">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th class="team-name">name of the team</th>
                                                    <th class="goal">goal</th>
                                                    <th class="choice-team">bet your choose team</th>
                                                    <th class="bet-num">total bet</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <div class="team-name-part">
                                                            <i class="fa fa-futbol-o"></i>
                                                            <div class="content">
                                                                <span class="name">Barcelona</span>
                                                                <span class="name">Real madrid</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="score">
                                                            <span class="first-team-score">2</span>
                                                            <span class="second-team-score">2</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="choice-team-part">
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-down"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="bet-count-num">+19<i
                                                                class="fa fa-angle-right"></i></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="team-name-part">
                                                            <i class="fa fa-futbol-o"></i>
                                                            <div class="content">
                                                                <span class="name">Barcelona</span>
                                                                <span class="name">Real madrid</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="score">
                                                            <span class="first-team-score">2</span>
                                                            <span class="second-team-score">2</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="choice-team-part">
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-down"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="bet-count-num">+19<i
                                                                class="fa fa-angle-right"></i></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="team-name-part">
                                                            <i class="fa fa-futbol-o"></i>
                                                            <div class="content">
                                                                <span class="name">Barcelona</span>
                                                                <span class="name">Real madrid</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="score">
                                                            <span class="first-team-score">2</span>
                                                            <span class="second-team-score">2</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="choice-team-part">
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-down"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="bet-count-num">+19<i
                                                                class="fa fa-angle-right"></i></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="team-name-part">
                                                            <i class="fa fa-futbol-o"></i>
                                                            <div class="content">
                                                                <span class="name">Barcelona</span>
                                                                <span class="name">Real madrid</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="score">
                                                            <span class="first-team-score">2</span>
                                                            <span class="second-team-score">2</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="choice-team-part">
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-down"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="bet-count-num">+19<i
                                                                class="fa fa-angle-right"></i></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="team-name-part">
                                                            <i class="fa fa-futbol-o"></i>
                                                            <div class="content">
                                                                <span class="name">Barcelona</span>
                                                                <span class="name">Real madrid</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="score">
                                                            <span class="first-team-score">2</span>
                                                            <span class="second-team-score">2</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="choice-team-part">
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-down"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="bet-count-num">+19<i
                                                                class="fa fa-angle-right"></i></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="team-name-part">
                                                            <i class="fa fa-futbol-o"></i>
                                                            <div class="content">
                                                                <span class="name">Barcelona</span>
                                                                <span class="name">Real madrid</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="score">
                                                            <span class="first-team-score">2</span>
                                                            <span class="second-team-score">2</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="choice-team-part">
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-down"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="bet-count-num">+19<i
                                                                class="fa fa-angle-right"></i></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="team-name-part">
                                                            <i class="fa fa-futbol-o"></i>
                                                            <div class="content">
                                                                <span class="name">Barcelona</span>
                                                                <span class="name">Real madrid</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="score">
                                                            <span class="first-team-score">2</span>
                                                            <span class="second-team-score">2</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="choice-team-part">
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-down"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="bet-count-num">+19<i
                                                                class="fa fa-angle-right"></i></span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="cricket" role="tabpanel" aria-labelledby="cricket-tab">
                                <div class="play-table-part">
                                    <div class="play-table">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th class="team-name">name of the team</th>
                                                    <th class="goal">goal</th>
                                                    <th class="choice-team">bet your choose team</th>
                                                    <th class="bet-num">total bet</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <div class="team-name-part">
                                                            <i class="flaticon-cricket"></i>
                                                            <div class="content">
                                                                <span class="name">Barcelona</span>
                                                                <span class="name">Real madrid</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="score">
                                                            <span class="first-team-score">2</span>
                                                            <span class="second-team-score">2</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="choice-team-part">
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-down"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="bet-count-num">+19<i
                                                                class="fa fa-angle-right"></i></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="team-name-part">
                                                            <i class="flaticon-cricket"></i>
                                                            <div class="content">
                                                                <span class="name">Barcelona</span>
                                                                <span class="name">Real madrid</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="score">
                                                            <span class="first-team-score">2</span>
                                                            <span class="second-team-score">2</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="choice-team-part">
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-down"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="bet-count-num">+19<i
                                                                class="fa fa-angle-right"></i></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="team-name-part">
                                                            <i class="flaticon-cricket"></i>
                                                            <div class="content">
                                                                <span class="name">Barcelona</span>
                                                                <span class="name">Real madrid</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="score">
                                                            <span class="first-team-score">2</span>
                                                            <span class="second-team-score">2</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="choice-team-part">
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-down"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="bet-count-num">+19<i
                                                                class="fa fa-angle-right"></i></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="team-name-part">
                                                            <i class="flaticon-cricket"></i>
                                                            <div class="content">
                                                                <span class="name">Barcelona</span>
                                                                <span class="name">Real madrid</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="score">
                                                            <span class="first-team-score">2</span>
                                                            <span class="second-team-score">2</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="choice-team-part">
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-down"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="bet-count-num">+19<i
                                                                class="fa fa-angle-right"></i></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="team-name-part">
                                                            <i class="flaticon-cricket"></i>
                                                            <div class="content">
                                                                <span class="name">Barcelona</span>
                                                                <span class="name">Real madrid</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="score">
                                                            <span class="first-team-score">2</span>
                                                            <span class="second-team-score">2</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="choice-team-part">
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-down"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="bet-count-num">+19<i
                                                                class="fa fa-angle-right"></i></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="team-name-part">
                                                            <i class="flaticon-cricket"></i>
                                                            <div class="content">
                                                                <span class="name">Barcelona</span>
                                                                <span class="name">Real madrid</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="score">
                                                            <span class="first-team-score">2</span>
                                                            <span class="second-team-score">2</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="choice-team-part">
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-down"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="bet-count-num">+19<i
                                                                class="fa fa-angle-right"></i></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="team-name-part">
                                                            <i class="flaticon-cricket"></i>
                                                            <div class="content">
                                                                <span class="name">Barcelona</span>
                                                                <span class="name">Real madrid</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="score">
                                                            <span class="first-team-score">2</span>
                                                            <span class="second-team-score">2</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="choice-team-part">
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-down"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="bet-count-num">+19<i
                                                                class="fa fa-angle-right"></i></span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="tennis" role="tabpanel" aria-labelledby="tennis-tab">
                                <div class="play-table-part">
                                    <div class="play-table">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th class="team-name">name of the team</th>
                                                    <th class="goal">goal</th>
                                                    <th class="choice-team">bet your choose team</th>
                                                    <th class="bet-num">total bet</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <div class="team-name-part">
                                                            <i class="flaticon-tennis-racket"></i>
                                                            <div class="content">
                                                                <span class="name">Barcelona</span>
                                                                <span class="name">Real madrid</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="score">
                                                            <span class="first-team-score">2</span>
                                                            <span class="second-team-score">2</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="choice-team-part">
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-down"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="bet-count-num">+19<i
                                                                class="fa fa-angle-right"></i></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="team-name-part">
                                                            <i class="flaticon-tennis-racket"></i>
                                                            <div class="content">
                                                                <span class="name">Barcelona</span>
                                                                <span class="name">Real madrid</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="score">
                                                            <span class="first-team-score">2</span>
                                                            <span class="second-team-score">2</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="choice-team-part">
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-down"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="bet-count-num">+19<i
                                                                class="fa fa-angle-right"></i></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="team-name-part">
                                                            <i class="flaticon-tennis-racket"></i>
                                                            <div class="content">
                                                                <span class="name">Barcelona</span>
                                                                <span class="name">Real madrid</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="score">
                                                            <span class="first-team-score">2</span>
                                                            <span class="second-team-score">2</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="choice-team-part">
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-down"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="bet-count-num">+19<i
                                                                class="fa fa-angle-right"></i></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="team-name-part">
                                                            <i class="flaticon-tennis-racket"></i>
                                                            <div class="content">
                                                                <span class="name">Barcelona</span>
                                                                <span class="name">Real madrid</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="score">
                                                            <span class="first-team-score">2</span>
                                                            <span class="second-team-score">2</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="choice-team-part">
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-down"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="bet-count-num">+19<i
                                                                class="fa fa-angle-right"></i></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="team-name-part">
                                                            <i class="flaticon-tennis-racket"></i>
                                                            <div class="content">
                                                                <span class="name">Barcelona</span>
                                                                <span class="name">Real madrid</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="score">
                                                            <span class="first-team-score">2</span>
                                                            <span class="second-team-score">2</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="choice-team-part">
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-down"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="bet-count-num">+19<i
                                                                class="fa fa-angle-right"></i></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="team-name-part">
                                                            <i class="flaticon-tennis-racket"></i>
                                                            <div class="content">
                                                                <span class="name">Barcelona</span>
                                                                <span class="name">Real madrid</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="score">
                                                            <span class="first-team-score">2</span>
                                                            <span class="second-team-score">2</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="choice-team-part">
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-down"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="bet-count-num">+19<i
                                                                class="fa fa-angle-right"></i></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="team-name-part">
                                                            <i class="flaticon-tennis-racket"></i>
                                                            <div class="content">
                                                                <span class="name">Barcelona</span>
                                                                <span class="name">Real madrid</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="score">
                                                            <span class="first-team-score">2</span>
                                                            <span class="second-team-score">2</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="choice-team-part">
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-down"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="bet-count-num">+19<i
                                                                class="fa fa-angle-right"></i></span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="bascketball" role="tabpanel"
                                aria-labelledby="bascketball-tab">
                                <div class="play-table-part">
                                    <div class="play-table">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th class="team-name">name of the team</th>
                                                    <th class="goal">goal</th>
                                                    <th class="choice-team">bet your choose team</th>
                                                    <th class="bet-num">total bet</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <div class="team-name-part">
                                                            <i class="flaticon-basketball"></i>
                                                            <div class="content">
                                                                <span class="name">Barcelona</span>
                                                                <span class="name">Real madrid</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="score">
                                                            <span class="first-team-score">2</span>
                                                            <span class="second-team-score">2</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="choice-team-part">
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-down"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="bet-count-num">+19<i
                                                                class="fa fa-angle-right"></i></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="team-name-part">
                                                            <i class="flaticon-basketball"></i>
                                                            <div class="content">
                                                                <span class="name">Barcelona</span>
                                                                <span class="name">Real madrid</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="score">
                                                            <span class="first-team-score">2</span>
                                                            <span class="second-team-score">2</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="choice-team-part">
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-down"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="bet-count-num">+19<i
                                                                class="fa fa-angle-right"></i></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="team-name-part">
                                                            <i class="flaticon-basketball"></i>
                                                            <div class="content">
                                                                <span class="name">Barcelona</span>
                                                                <span class="name">Real madrid</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="score">
                                                            <span class="first-team-score">2</span>
                                                            <span class="second-team-score">2</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="choice-team-part">
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-down"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="bet-count-num">+19<i
                                                                class="fa fa-angle-right"></i></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="team-name-part">
                                                            <i class="flaticon-basketball"></i>
                                                            <div class="content">
                                                                <span class="name">Barcelona</span>
                                                                <span class="name">Real madrid</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="score">
                                                            <span class="first-team-score">2</span>
                                                            <span class="second-team-score">2</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="choice-team-part">
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-down"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="bet-count-num">+19<i
                                                                class="fa fa-angle-right"></i></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="team-name-part">
                                                            <i class="flaticon-basketball"></i>
                                                            <div class="content">
                                                                <span class="name">Barcelona</span>
                                                                <span class="name">Real madrid</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="score">
                                                            <span class="first-team-score">2</span>
                                                            <span class="second-team-score">2</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="choice-team-part">
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-down"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="bet-count-num">+19<i
                                                                class="fa fa-angle-right"></i></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="team-name-part">
                                                            <i class="flaticon-basketball"></i>
                                                            <div class="content">
                                                                <span class="name">Barcelona</span>
                                                                <span class="name">Real madrid</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="score">
                                                            <span class="first-team-score">2</span>
                                                            <span class="second-team-score">2</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="choice-team-part">
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-down"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="bet-count-num">+19<i
                                                                class="fa fa-angle-right"></i></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="team-name-part">
                                                            <i class="flaticon-basketball"></i>
                                                            <div class="content">
                                                                <span class="name">Barcelona</span>
                                                                <span class="name">Real madrid</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="score">
                                                            <span class="first-team-score">2</span>
                                                            <span class="second-team-score">2</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="choice-team-part">
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-down"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="bet-count-num">+19<i
                                                                class="fa fa-angle-right"></i></span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="baseball" role="tabpanel" aria-labelledby="baseball-tab">
                                <div class="play-table-part">
                                    <div class="play-table">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th class="team-name">name of the team</th>
                                                    <th class="goal">goal</th>
                                                    <th class="choice-team">bet your choose team</th>
                                                    <th class="bet-num">total bet</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <div class="team-name-part">
                                                            <i class="flaticon-softball"></i>
                                                            <div class="content">
                                                                <span class="name">Barcelona</span>
                                                                <span class="name">Real madrid</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="score">
                                                            <span class="first-team-score">2</span>
                                                            <span class="second-team-score">2</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="choice-team-part">
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-down"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="bet-count-num">+19<i
                                                                class="fa fa-angle-right"></i></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="team-name-part">
                                                            <i class="flaticon-softball"></i>
                                                            <div class="content">
                                                                <span class="name">Barcelona</span>
                                                                <span class="name">Real madrid</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="score">
                                                            <span class="first-team-score">2</span>
                                                            <span class="second-team-score">2</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="choice-team-part">
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-down"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="bet-count-num">+19<i
                                                                class="fa fa-angle-right"></i></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="team-name-part">
                                                            <i class="flaticon-softball"></i>
                                                            <div class="content">
                                                                <span class="name">Barcelona</span>
                                                                <span class="name">Real madrid</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="score">
                                                            <span class="first-team-score">2</span>
                                                            <span class="second-team-score">2</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="choice-team-part">
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-down"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="bet-count-num">+19<i
                                                                class="fa fa-angle-right"></i></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="team-name-part">
                                                            <i class="flaticon-softball"></i>
                                                            <div class="content">
                                                                <span class="name">Barcelona</span>
                                                                <span class="name">Real madrid</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="score">
                                                            <span class="first-team-score">2</span>
                                                            <span class="second-team-score">2</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="choice-team-part">
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-down"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="bet-count-num">+19<i
                                                                class="fa fa-angle-right"></i></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="team-name-part">
                                                            <i class="flaticon-softball"></i>
                                                            <div class="content">
                                                                <span class="name">Barcelona</span>
                                                                <span class="name">Real madrid</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="score">
                                                            <span class="first-team-score">2</span>
                                                            <span class="second-team-score">2</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="choice-team-part">
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-down"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="bet-count-num">+19<i
                                                                class="fa fa-angle-right"></i></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="team-name-part">
                                                            <i class="flaticon-softball"></i>
                                                            <div class="content">
                                                                <span class="name">Barcelona</span>
                                                                <span class="name">Real madrid</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="score">
                                                            <span class="first-team-score">2</span>
                                                            <span class="second-team-score">2</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="choice-team-part">
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-down"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="bet-count-num">+19<i
                                                                class="fa fa-angle-right"></i></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="team-name-part">
                                                            <i class="flaticon-softball"></i>
                                                            <div class="content">
                                                                <span class="name">Barcelona</span>
                                                                <span class="name">Real madrid</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="score">
                                                            <span class="first-team-score">2</span>
                                                            <span class="second-team-score">2</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="choice-team-part">
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-down"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="bet-count-num">+19<i
                                                                class="fa fa-angle-right"></i></span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="more" role="tabpanel" aria-labelledby="more-tab">
                                <div class="play-table-part">
                                    <div class="play-table">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th class="team-name">name of the team</th>
                                                    <th class="goal">goal</th>
                                                    <th class="choice-team">bet your choose team</th>
                                                    <th class="bet-num">total bet</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <div class="team-name-part">
                                                            <i class="fa fa-futbol-o"></i>
                                                            <div class="content">
                                                                <span class="name">Barcelona</span>
                                                                <span class="name">Real madrid</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="score">
                                                            <span class="first-team-score">2</span>
                                                            <span class="second-team-score">2</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="choice-team-part">
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-down"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="bet-count-num">+19<i
                                                                class="fa fa-angle-right"></i></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="team-name-part">
                                                            <i class="fa fa-futbol-o"></i>
                                                            <div class="content">
                                                                <span class="name">Barcelona</span>
                                                                <span class="name">Real madrid</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="score">
                                                            <span class="first-team-score">2</span>
                                                            <span class="second-team-score">2</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="choice-team-part">
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-down"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="bet-count-num">+19<i
                                                                class="fa fa-angle-right"></i></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="team-name-part">
                                                            <i class="fa fa-futbol-o"></i>
                                                            <div class="content">
                                                                <span class="name">Barcelona</span>
                                                                <span class="name">Real madrid</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="score">
                                                            <span class="first-team-score">2</span>
                                                            <span class="second-team-score">2</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="choice-team-part">
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-down"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="bet-count-num">+19<i
                                                                class="fa fa-angle-right"></i></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="team-name-part">
                                                            <i class="fa fa-futbol-o"></i>
                                                            <div class="content">
                                                                <span class="name">Barcelona</span>
                                                                <span class="name">Real madrid</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="score">
                                                            <span class="first-team-score">2</span>
                                                            <span class="second-team-score">2</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="choice-team-part">
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-down"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="bet-count-num">+19<i
                                                                class="fa fa-angle-right"></i></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="team-name-part">
                                                            <i class="fa fa-futbol-o"></i>
                                                            <div class="content">
                                                                <span class="name">Barcelona</span>
                                                                <span class="name">Real madrid</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="score">
                                                            <span class="first-team-score">2</span>
                                                            <span class="second-team-score">2</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="choice-team-part">
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-down"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="bet-count-num">+19<i
                                                                class="fa fa-angle-right"></i></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="team-name-part">
                                                            <i class="fa fa-futbol-o"></i>
                                                            <div class="content">
                                                                <span class="name">Barcelona</span>
                                                                <span class="name">Real madrid</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="score">
                                                            <span class="first-team-score">2</span>
                                                            <span class="second-team-score">2</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="choice-team-part">
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-down"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="bet-count-num">+19<i
                                                                class="fa fa-angle-right"></i></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="team-name-part">
                                                            <i class="fa fa-futbol-o"></i>
                                                            <div class="content">
                                                                <span class="name">Barcelona</span>
                                                                <span class="name">Real madrid</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="score">
                                                            <span class="first-team-score">2</span>
                                                            <span class="second-team-score">2</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="choice-team-part">
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-down"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                            <div class="single-item">
                                                                <span>Barcelona</span>
                                                                <p>2.5<i class="fa fa-angle-up"></i></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="bet-count-num">+19<i
                                                                class="fa fa-angle-right"></i></span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- online-play-tab-part end -->
                </div>
            </div>
        </div>
    </section>
    <!-- play-section end -->

    <!-- download-app-section start -->
    
	
	<section class="download-app-section">
        <div class="download-app-wrapper d-flex">
         <div class="col-md-6 col-sm-6">

               <img class="video-image img-responsive" src="{{asset('assets/images/Video_part_2.png')}}">
           </div>
		   
		   <div class="col-md-6 col-sm-6" style="background-color:var(--second-color);">
		   
                 <div class="content">
                    <h2 class="title" style="color:white;">{{__('messages.download_our_app')}}</h2>
					</br>
                    <p style="color:white;">{{__('messages.download_our_app_paragraph')}}</p>
                    <ul class="app-features-list">
                        <li><i class="fa fa-check"></i>{{__('messages.download_our_app_point1')}}</li>
                        <li><i class="fa fa-check"></i>{{__('messages.download_our_app_point2')}}</li>
                        <li><i class="fa fa-check"></i>{{__('messages.download_our_app_point3')}}</li>
                        <li><i class="fa fa-check"></i>{{__('messages.download_our_app_point4')}}</li>
                    </ul>
                    <div class="btn-area">
                        <a href="#" class="cmn-btn btn-lg"><i class="fa fa-play"></i>play store</a>
                        <a href="#" class="cmn-btn border-btn btn-lg "><i class="fa fa-apple"></i>app store</a>
                    </div>
                </div>
				
            
           </div>
           
        </div>
    </section>
    <!-- download-app-section end -->

    <!-- step-section start -->
    <section class="step-section section-padding">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="section-header text-center">
                        <h2 class="section-title">{{__('messages.play_with_butlar')}}</h2>
                        <p>{{__('messages.play_with_butlar_desc')}}</p>
                    </div>
                </div>
            </div>
            <div class="row step-line margin-bottom-not-8">
                <div class="col-lg-4 col-md-4">
                    <div class="step-item text-center">
                        <div class="icon"><i class="fa fa-sign-in"></i></div>
                        <h3 class="title">{{__('messages.sign_up')}}</h3>
                        <p>{{__('messages.signup_text')}}</p>
                    </div>
                </div><!-- step-item end -->
                <div class="col-lg-4 col-md-4">
                    <div class="step-item text-center">
                        <div class="icon"><i class="flaticon-wallet"></i></div>
                        <h3 class="title">{{__('messages.deposit')}}</h3>
                        <p>{{__('messages.deposit_text')}}</p>
                    </div>
                </div><!-- step-item end -->
                <div class="col-lg-4 col-md-4">
                    <div class="step-item text-center">
                        <div class="icon"><i class="flaticon-crown"></i></div>
                        <h3 class="title">{{__('messages.play')}}</h3>
                        <p>{{__('messages.play_text')}}</p>
                    </div>
                </div><!-- step-item end -->
            </div>
        </div>
    </section>
    <!-- step-section end -->

    <!-- service-section start -->
    <section class="service-section section-padding section-bg" style="display:none">
        <div class="service-chart-image">
            <img src="assets/images/elements/macbook.png" alt="image">
        </div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <div class="section-header text-center">
                        <h2 class="section-title">Some of amazing Service</h2>
                        <p>On insensible possession oh particular attachment at excellence in. The books arose but miles
                            happy she.</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-6">
                    <div class="row margin-bottom-not-30">
                        <div class="col-xl-12 col-lg-6">
                            <div class="service-item d-flex">
                                <div class="icon">
                                    <i class="flaticon-race-horse"></i>
                                </div>
                                <div class="content">
                                    <h3 class="title">Instant Sports Match Betting</h3>
                                    <p>Her extensive perceived may any sincerity extremity. Indeed add rather may pretty
                                        see</p>
                                </div>
                            </div><!-- service-item end -->
                        </div>
                        <div class="col-xl-12 col-lg-6">
                            <div class="service-item d-flex">
                                <div class="icon">
                                    <i class="flaticon-employee"></i>
                                </div>
                                <div class="content">
                                    <h3 class="title">Player by Choice Betting</h3>
                                    <p>Her extensive perceived may any sincerity extremity. Indeed add rather may pretty
                                        see</p>
                                </div>
                            </div><!-- service-item end -->
                        </div>
                        <div class="col-xl-12 col-lg-6">
                            <div class="service-item d-flex">
                                <div class="icon">
                                    <i class="flaticon-cricket-1"></i>
                                </div>
                                <div class="content">
                                    <h3 class="title">Ball by Ball Betting Cricket</h3>
                                    <p>Her extensive perceived may any sincerity extremity. Indeed add rather may pretty
                                        see</p>
                                </div>
                            </div><!-- service-item end -->
                        </div>
                        <div class="col-xl-12 col-lg-6">
                            <div class="service-item d-flex">
                                <div class="icon">
                                    <i class="flaticon-development"></i>
                                </div>
                                <div class="content">
                                    <h3 class="title">Goal to Play Goal Opportunity</h3>
                                    <p>Her extensive perceived may any sincerity extremity. Indeed add rather may pretty
                                        see</p>
                                </div>
                            </div><!-- service-item end -->
                        </div>
                        <div class="col-xl-12 col-lg-6">
                            <div class="service-item d-flex">
                                <div class="icon">
                                    <i class="flaticon-trophy"></i>
                                </div>
                                <div class="content">
                                    <h3 class="title">Lot of Chance to Winning</h3>
                                    <p>Her extensive perceived may any sincerity extremity. Indeed add rather may pretty
                                        see</p>
                                </div>
                            </div><!-- service-item end -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- service-section end -->

    <!-- testimonial-section start -->
    <section class="testimonial-section section-padding" style="display:none">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="content">
                        <h2 class="title">Check What's Our Client Say About us</h2>
                        <p>On insensible possession oh particular attachment at excellence in. The books arose but miles
                            happy she.</p>
                        <div class="testimonial-slider">
                            <div class="testimonial-slide">
                                <h5 class="name">Steven Hatfield</h5>
                                <p>Remember outweigh do he desirous no cheerful. Do of doors water ye guest. We if
                                    prosperous comparison middletons at. Park we in lose like at no. An so to preferred
                                    convinced distrusts he determine.</p>
                            </div><!-- testimonial-slide end -->
                            <div class="testimonial-slide">
                                <h5 class="name">Steven Hatfield</h5>
                                <p>Remember outweigh do he desirous no cheerful. Do of doors water ye guest. We if
                                    prosperous comparison middletons at. Park we in lose like at no. An so to preferred
                                    convinced distrusts he determine.</p>
                            </div><!-- testimonial-slide end -->
                            <div class="testimonial-slide">
                                <h5 class="name">Steven Hatfield</h5>
                                <p>Remember outweigh do he desirous no cheerful. Do of doors water ye guest. We if
                                    prosperous comparison middletons at. Park we in lose like at no. An so to preferred
                                    convinced distrusts he determine.</p>
                            </div><!-- testimonial-slide end -->
                        </div><!-- testimonial-slider end -->
                        <ul class="testimonial-slider-arrows">
                            <li class="prev" id="testimonial-slider-prev">
                                <i class="fa fa-long-arrow-left"></i>
                            </li>
                            <li class="next" id="testimonial-slider-next">
                                <i class="fa fa-long-arrow-right"></i>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="testmonial-thumb-part">
                        <div class="testmonial-thumb">
                            <img src="assets/images/testmonial.png" alt="image">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- testimonial-section end -->

    <!-- play-butlar-section start -->
    <section class="play-butlar-section section-padding">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <div class="section-header text-center">
                        <h2 class="section-title">{{__('messages.play_with_bet_plus')}}</h2>
                        <p>{{__('messages.play_with_bet_plus_text')}}</p>
                    </div>
                </div>
            </div>
            <div class="play-butlar-slider">

            <div class="play-item text-center">
                    <div class="thumb">
                        <img src="assets/images/play/4.png" alt="image">
                    </div>
                    <div class="content">
                        <h3 class="title"><a href="#">Football</a></h3>
                        <p></p>
                    </div>
                </div>
                <div class="play-item text-center">
                    <div class="thumb">
                        <img src="assets/images/play/2.png" alt="image">
                    </div>
                    <div class="content">
                        <h3 class="title"><a href="#">Basket Ball</a></h3>
                        <p></p>
                    </div>
                </div>
                <div class="play-item text-center">
                    <div class="thumb">
                        <img src="assets/images/play/3.png" alt="image">
                    </div>
                    <div class="content">
                        <h3 class="title"><a href="#">Tennis</a></h3>
                        <p></p>
                    </div>
                </div>
                <div class="play-item text-center">
                    <div class="thumb">
                        <img src="assets/images/play/1.png" alt="image">
                    </div>
                    <div class="content">
                        <h3 class="title"><a href="#">Hockey sur glace</a></h3>
                        <p></p>
                    </div>
                </div>  
            </div>
            <ul class="play-butlar-slider-arrows">
                <li class="prev" id="play-butlar-slider-prev">
                    <i class="fa fa-long-arrow-left" aria-hidden="true"></i>
                </li>
                <li class="next" id="play-butlar-slider-next">
                    <i class="fa fa-long-arrow-right" aria-hidden="true"></i>
                </li>
            </ul>
        </div>
    </section>
    <!-- play-butlar-section end -->

    <!-- blog-section start -->
    <section class="blog-section section-padding section-bg" style="display:none">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <div class="section-header text-center">
                        <h2 class="section-title">blog</h2>
                        <p>On insensible possession oh particular attachment at excellence in. The books arose but miles
                            happy she.</p>
                    </div>
                </div>
            </div>
            <div class="row mt-mb-15">
                <div class="col-lg-4 col-sm-6">
                    <div class="post-item">
                        <div class="thumb">
                            <img src="assets/images/blog/1.jpg" alt="image">
                        </div>
                        <div class="content">
                            <ul class="post-meta">
                                <li><a href="#"><i class="fa fa-calendar"></i>04, March, 2019</a></li>
                            </ul>
                            <h5 class="post-title"><a href="#0">Turn Your Bet Into A High Performing Machine</a></h5>
                            <p>No depending be convinced in unfeeling he. Excellence she unaffected and too sentiments
                                her.</p>
                        </div>
                    </div>
                </div><!-- post-item end -->
                <div class="col-lg-4 col-sm-6">
                    <div class="post-item">
                        <div class="thumb">
                            <img src="assets/images/blog/2.jpg" alt="image">
                        </div>
                        <div class="content">
                            <ul class="post-meta">
                                <li><a href="#"><i class="fa fa-calendar"></i>04, March, 2019</a></li>
                            </ul>
                            <h5 class="post-title"><a href="#0">BET Is Crucial To Your Business. Learn Why!</a></h5>
                            <p>No depending be convinced in unfeeling he. Excellence she unaffected and too sentiments
                                her.</p>
                        </div>
                    </div>
                </div><!-- post-item end -->
                <div class="col-lg-4 col-sm-6">
                    <div class="post-item">
                        <div class="thumb">
                            <img src="assets/images/blog/3.jpg" alt="image">
                        </div>
                        <div class="content">
                            <ul class="post-meta">
                                <li><a href="#"><i class="fa fa-calendar"></i>04, March, 2019</a></li>
                            </ul>
                            <h5 class="post-title"><a href="#0">Why Most People Will Never Be Great At BET</a></h5>
                            <p>No depending be convinced in unfeeling he. Excellence she unaffected and too sentiments
                                her.</p>
                        </div>
                    </div>
                </div><!-- post-item end -->
            </div>
        </div>
    </section>
    <!-- blog-section end -->
@endsection
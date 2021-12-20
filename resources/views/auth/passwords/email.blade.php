@extends('layouts.app')

@section('content')
    <section class="breadcum-section">
		<div class="breadcum-area">
			<div class="container">
				<div class="row">
					<div class="col-lg-12">
						<div class="breadcum-content text-center">
							<h3 class="title">Forgot Password</h3>
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="index.html">home</a></li>
								<li class="breadcrumb-item active">Forgot Password</li>
							</ol>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- banner-section end -->

	<section class="login-section section-padding">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-lg-10">
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            {{ session('error') }}
                        </div>
                    @endif
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            {{ session('success') }}
                        </div>
                    @endif
					<div class="login-block text-center">
						<div class="login-block-inner">
							<h3 class="title">Forgot Your Password </h3>
							<form class="cmn-form login-form"  action="{{ route('post-forgot-password') }}" id="forgot-password-form" method="post">
                            @csrf
                                
								<div class="frm-group">
									<input type="text" name="email" id="email" placeholder="Your Email">
								</div>
								
								<div class="frm-group">
									<button type="submit" class="submit-btn">Submit</button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
    </section>
	<script src="http://code.jquery.com/jquery-1.11.1.js"></script>
	<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.0/jquery.validate.min.js"></script>

    <script>
		$(document).ready(function () {
			$('#forgot-password-form').validate({ // initialize the plugin
				rules: {
					email: {
						required: true,
						email: true
					}
				},
				messages:{
					email: {
						required: "Please enter email",
						email: "Please enter valid email address"
					}
					
				}
			}); 
		});
	</script>
@endsection
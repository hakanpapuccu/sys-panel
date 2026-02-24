        <!--**********************************
            Footer start
        ***********************************-->
        <div class="footer">
            <div class="copyright">
                <p>Copyright © Designed &amp; Developed by <a href="https://dexignlab.com/" target="_blank" rel="noopener noreferrer">DexignLab</a> 2021</p>
            </div>
        </div>
        <!--**********************************
            Footer end
        ***********************************-->

		<!--**********************************
           Support ticket button start
        ***********************************-->

        <!--**********************************
           Support ticket button end
        ***********************************-->



	</div>
    <!--**********************************
        Main wrapper end
    ***********************************-->

	    <!--**********************************
	        Scripts
	    ***********************************-->
	    <!-- Required vendors -->
	    <script src="{{ asset('vendor/global/global.min.js') }}"></script>
		<script src="{{ asset('vendor/jquery-nice-select/js/jquery.nice-select.min.js') }}"></script>
	    <script src="{{ asset('js/custom.js') }}"></script>
		<script src="{{ asset('js/dlabnav-init.js') }}"></script>

		@if(request()->routeIs('vacations', 'tasks.index'))
	    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
	    <script src="{{ asset('js/plugins-init/datatables.init.js') }}"></script>
		@endif

		@if(request()->routeIs('vacations'))
	    <script src="{{ asset('vendor/clockpicker/js/bootstrap-clockpicker.min.js') }}"></script>
	    <script src="{{ asset('js/plugins-init/clock-picker-init.js') }}"></script>
		@endif

	    <script src="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>

@stack('scripts')

	    <script>
	        @if(Session::has('success'))
	            Swal.fire({
                icon: 'success',
                title: 'Başarılı',
                text: "{{ session('success') }}",
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        @if(Session::has('message'))
            Swal.fire({
                icon: 'success',
                title: 'Başarılı',
                text: "{{ session('message') }}",
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        @if(Session::has('error'))
            Swal.fire({
                icon: 'error',
                title: 'Hata',
                text: "{{ session('error') }}",
            });
        @endif

        @if(Session::has('info'))
            Swal.fire({
                icon: 'info',
                title: 'Bilgi',
                text: "{{ session('info') }}",
            });
        @endif

        @if(Session::has('warning'))
            Swal.fire({
                icon: 'warning',
                title: 'Uyarı',
                text: "{{ session('warning') }}",
            });
	        @endif
	    </script>


	</body>
</html>

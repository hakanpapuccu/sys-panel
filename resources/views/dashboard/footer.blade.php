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
    <script src={{asset("vendor/global/global.min.js")}}></script>
    <script src={{asset("vendor/chart.js/Chart.bundle.min.js")}}></script>
    <!-- Apex Chart -->
	<script src={{asset("vendor/apexchart/apexchart.js")}}></script>
    <script src={{asset("vendor/datatables/js/jquery.dataTables.min.js")}}></script>
    <script src={{asset("js/plugins-init/datatables.init.js")}}></script>

      <!-- Daterangepicker -->
    <!-- momment js is must -->
    <script src={{asset("vendor/moment/moment.min.js")}}></script>
    <script src={{asset("vendor/bootstrap-daterangepicker/daterangepicker.js")}}></script>
    <!-- clockpicker -->
    <script src={{asset("vendor/clockpicker/js/bootstrap-clockpicker.min.js")}}></script>
    <!-- asColorPicker -->
    <script src={{asset("vendor/jquery-asColor/jquery-asColor.min.js")}}></script>
    <script src={{asset("vendor/jquery-asGradient/jquery-asGradient.min.js")}}></script>
    <script src={{asset("vendor/jquery-asColorPicker/js/jquery-asColorPicker.min.js")}}></script>
    <!-- Material color picker -->
    <script src={{asset("vendor/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js")}}></script>
    <!-- pickdate -->
    <script src={{asset("vendor/pickadate/picker.js")}}></script>
    <script src={{asset("vendor/pickadate/picker.time.js")}}></script>
    <script src={{asset("vendor/pickadate/picker.date.js")}}></script>



    <!-- Daterangepicker -->
    <script src={{asset("js/plugins-init/bs-daterange-picker-init.js")}}></script>
    <!-- Clockpicker init -->
    <script src={{asset("js/plugins-init/clock-picker-init.js")}}></script>
    <!-- asColorPicker init -->
    <script src={{asset("js/plugins-init/jquery-asColorPicker.init.js")}}></script>
    <!-- Material color picker init -->
    <script src={{asset("js/plugins-init/material-date-picker-init.js")}}></script>
    <!-- Pickdate -->
    <script src={{asset("js/plugins-init/pickadate-init.js")}}></script>

	<script src={{asset("vendor/jquery-nice-select/js/jquery.nice-select.min.js")}}></script>
    <script src={{asset("js/custom.js")}}></script>
	<script src={{asset("js/dlabnav-init.js")}}></script>
    <script src="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>
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

@stack('scripts')
	
   
</body>
</html>

$(document).ready(function () {
	$(".uang").mask("000.000.000.000.000", {
		reverse: true,
	});

	updateTotalItem();

	$("select[name='direksi']").change(function () {
		var val = $(this).val();
		if (val == 1) {
			$("#nama_direksi").attr("disabled", false);
		} else {
			$("#nama_direksi").attr("disabled", true);
		}
	});

	$("#upload-bayar").hide();
	$("#jenis-pembayaran").change(function () {
		var val = $(this).val();
		if (val == "kas") {
			$("#upload-bayar").show();
		} else {
			$("#upload-bayar").hide();
		}
	});

	// Saat input qty atau harga diubah
	$(document).on(
		"input",
		'input[name="qty[]"], input[name="harga[]"]',
		function () {
			var value = $(this).val();
			var row = $(this).closest(".baris");
			hitungTotal(row);
			updateTotalItem();
		}
	);

	$(".btn-submit").click(function (e) {
		e.preventDefault();
		var parent = $(this).parents("form");
		var url = parent.attr("action");
		console.log(parent);
		var formData = new FormData(parent[0]);
		Swal.fire({
			title: "Are you sure?",
			text: "You want to submit the form?",
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: "#3085d6",
			cancelButtonColor: "#d33",
			confirmButtonText: "Yes",
		}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					url: url,
					method: "POST",
					data: formData,
					processData: false,
					contentType: false,
					dataType: "JSON",
					beforeSend: () => {
						Swal.fire({
							title: "Loading....",
							timerProgressBar: true,
							allowOutsideClick: false,
							didOpen: () => {
								Swal.showLoading();
							},
						});
					},
					success: function (res) {
						if (res.success) {
							Swal.fire({
								icon: "success",
								title: `${res.msg}`,
								showConfirmButton: false,
								timer: 1500,
							}).then(function () {
								Swal.close();
								location.reload();
							});
						} else {
							Swal.fire({
								icon: "error",
								title: `${res.msg}`,
								showConfirmButton: false,
								timer: 1500,
							}).then(function () {
								Swal.close();
							});
						}
					},
					error: function (xhr, status, error) {
						console.log(xhr);
						Swal.fire({
							icon: "error",
							title: `${error}`,
							showConfirmButton: false,
							timer: 1500,
						});
					},
				});
			}
		});
	});
});

function updateTotalItem() {
	var total_pos_fix = 0;
	$(".baris").each(function () {
		var total = $(this).find('input[name="total[]"]').val().replace(/\./g, ""); // Ambil nilai total dari setiap baris
		total = parseFloat(total); // Ubah string ke angka float
		if (!isNaN(total)) {
			// Pastikan total adalah angka
			total_pos_fix += total; // Tambahkan nilai total ke total_pos_fix
		}
	});
	$("#nominal").val(formatNumber(total_pos_fix)); // Atur nilai input #nominal dengan total_pos_fix
}

function formatNumber(number) {
	return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function hitungTotal(row) {
	var qty = row.find('input[name="qty[]"]').val().replace(/\./g, ""); // Hapus tanda titik
	var harga = row.find('input[name="harga[]"]').val().replace(/\./g, ""); // Hapus tanda titik
	qty = parseInt(qty); // Ubah string ke angka float
	harga = parseInt(harga); // Ubah string ke angka float

	qty = isNaN(qty) ? 0 : qty;
	harga = isNaN(harga) ? 0 : harga;

	var total = qty * harga;
	row.find('input[name="total[]"]').val(formatNumber(total));
	updateTotalItem();
}

function updateTotalItem() {
	var total_pos_fix = 0;
	$(".baris").each(function () {
		var total = $(this).find('input[name="total[]"]').val().replace(/\./g, ""); // Ambil nilai total dari setiap baris
		total = parseFloat(total); // Ubah string ke angka float
		if (!isNaN(total)) {
			// Pastikan total adalah angka
			total_pos_fix += total; // Tambahkan nilai total ke total_pos_fix
		}
	});
	$("#nominal").val(formatNumber(total_pos_fix)); // Atur nilai input #nominal dengan total_pos_fix
}

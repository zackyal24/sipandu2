const getPool = require('../../config/database');
const XLSX = require('xlsx');

module.exports = async (req, res) => {
  try {
    const pool = getPool();
    
    // Get all ubinan data
    const result = await pool.query(`
      SELECT 
        id,
        nama_petani,
        desa,
        kecamatan,
        TO_CHAR(tanggal_panen, 'YYYY-MM-DD') as tanggal_panen,
        subround,
        nomor_segmen,
        nomor_sub_segmen,
        berat_plot,
        gkp,
        gkg,
        ku,
        status,
        TO_CHAR(created_at, 'YYYY-MM-DD HH24:MI:SS') as created_at
      FROM monitoring_data_panen
      ORDER BY created_at DESC
    `);

    const data = result.rows;

    // Prepare Excel data with No column
    const excelData = data.map((row, index) => ({
      'No': index + 1,
      'Tanggal Panen': row.tanggal_panen,
      'Nama Petani': row.nama_petani,
      'Desa': row.desa,
      'Kecamatan': row.kecamatan,
      'Subround': row.subround,
      'Nomor Segmen': row.nomor_segmen,
      'Sub Segmen': row.nomor_sub_segmen,
      'Berat Plot (kg)': row.berat_plot,
      'GKP (ku/ha)': row.gkp,
      'GKG (ku/ha)': row.gkg,
      'Produksi Beras (ku)': row.ku,
      'Status': row.status
    }));

    // Create workbook and worksheet
    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.json_to_sheet(excelData);

    // Set column widths
    ws['!cols'] = [
      { wch: 6 },  // No
      { wch: 15 }, // Tanggal Panen
      { wch: 25 }, // Nama Petani
      { wch: 20 }, // Desa
      { wch: 20 }, // Kecamatan
      { wch: 10 }, // Subround
      { wch: 15 }, // Nomor Segmen
      { wch: 12 }, // Sub Segmen
      { wch: 15 }, // Berat Plot
      { wch: 12 }, // GKP
      { wch: 12 }, // GKG
      { wch: 15 }, // Produksi Beras
      { wch: 15 }  // Status
    ];

    XLSX.utils.book_append_sheet(wb, ws, 'Data Ubinan');

    // Generate buffer
    const buffer = XLSX.write(wb, { type: 'buffer', bookType: 'xlsx' });

    // Set response headers
    const filename = `Data_Ubinan_${new Date().toISOString().split('T')[0]}.xlsx`;
    res.setHeader('Content-Disposition', `attachment; filename="${filename}"`);
    res.setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

    return res.send(buffer);

  } catch (error) {
    console.error('Export error:', error);
    return res.status(500).json({ error: 'Gagal export data', details: error.message });
  }
};

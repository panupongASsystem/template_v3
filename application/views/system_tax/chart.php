<script>
      // กราฟ --------------------
  $(document).ready(function() {
    // 1. กราฟเส้นแสดงสถิติรายเดือน
    var monthlyData = <?php echo json_encode($monthly_data); ?>;
    var months = monthlyData.map(item => getThaiMonth(item.month));
    var transactions = monthlyData.map(item => parseInt(item.total_transactions));
    var amounts = monthlyData.map(item => parseFloat(item.total_amount));

    var monthlyOptions = {
      series: [{
        name: 'จำนวนรายการ',
        type: 'line',
        data: transactions
      }, {
        name: 'ยอดเงินรวม (บาท)',
        type: 'line',
        data: amounts
      }],
      chart: {
        height: 350,
        type: 'line',
        toolbar: {
          show: true
        }
      },
      stroke: {
        curve: 'smooth',
        width: [3, 3]
      },
      colors: ['#1A56DB', '#047857'],
      xaxis: {
        categories: months
      },
      yaxis: [{
        title: {
          text: 'จำนวนรายการ'
        }
      }, {
        opposite: true,
        title: {
          text: 'ยอดเงิน (บาท)'
        },
        labels: {
          formatter: (value) => new Intl.NumberFormat('th-TH').format(value)
        }
      }],
      markers: {
        size: 4
      },
      tooltip: {
        shared: true,
        intersect: false,
        y: [{
          formatter: value => value + " รายการ"
        }, {
          formatter: value => new Intl.NumberFormat('th-TH').format(value) + " บาท"
        }]
      }
    };

    // 2. กราฟวงกลมประเภทภาษี
    var taxTypeData = <?php echo json_encode($tax_type_data); ?>;
    var taxTypeOptions = {
      series: taxTypeData.map(item => parseInt(item.total_count)),
      chart: {
        width: 380, // กำหนดความกว้าง
        type: 'pie'
      },
      labels: taxTypeData.map(item => {
        const taxTypes = {
          'land': 'ภาษีที่ดินและสิ่งปลูกสร้าง',
          'signboard': 'ภาษีป้าย',
          'local': 'ภาษีท้องถิ่น'
        };
        return taxTypes[item.tax_type] || item.tax_type;
      }),
      colors: ['#60A5FA', '#34D399', '#F59E0B'],
      plotOptions: {
        pie: {
          expandOnClick: true,
          donut: {
            size: '65%'
          }
        }
      },
      legend: {
        position: 'bottom',
        horizontalAlign: 'center'
      },
      dataLabels: {
        enabled: true,
        formatter: function(val, opts) {
          return opts.w.config.series[opts.seriesIndex] + ' รายการ';
        }
      },
      tooltip: {
        y: {
          formatter: function(value) {
            return value + " รายการ";
          }
        }
      }
    };

    // 3. กราฟวงกลมสถานะการชำระ
    var statusData = <?php echo json_encode($status_data); ?>;
    var statusOptions = {
      series: statusData.map(item => parseInt(item.total_count)),
      chart: {
        width: 380, // กำหนดความกว้าง
        type: 'pie'
      },
      labels: statusData.map(item => {
        const statusTypes = {
          'pending': 'รอตรวจสอบ',
          'verified': 'อนุมัติแล้ว',
          'rejected': 'ปฏิเสธ',
          'arrears': 'ค้างชำระ'
        };
        return statusTypes[item.payment_status] || item.payment_status;
      }),
      colors: ['#FBBF24', '#10B981', '#EF4444', '#6e6d6d'],
      plotOptions: {
        pie: {
          expandOnClick: true,
          donut: {
            size: '65%'
          }
        }
      },
      legend: {
        position: 'bottom',
        horizontalAlign: 'center'
      },
      dataLabels: {
        enabled: true,
        formatter: function(val, opts) {
          return opts.w.config.series[opts.seriesIndex] + ' รายการ';
        }
      },
      tooltip: {
        y: {
          formatter: function(value) {
            return value + " รายการ";
          }
        }
      }
    };

    // 4. กราฟเปรียบเทียบยอดชำระภาษีแต่ละประเภท
    var taxYearlyData = <?php echo json_encode($tax_yearly_data); ?>;
    var arrearsData = <?php echo json_encode($arrears_data); ?>;

    // Process yearly data for comparison chart
    function processTaxYearlyData(data) {
      const months = Array.from({
        length: 12
      }, (_, i) => i + 1);
      const taxTypes = [...new Set(data.map(item => item.tax_type))];

      const series = taxTypes.map(type => {
        const monthlyData = months.map(month => {
          const entry = data.find(item =>
            item.tax_type === type && parseInt(item.month) === month
          );
          return entry ? parseFloat(entry.total_amount) : 0;
        });

        const taxTypeLabels = {
          'land': 'ภาษีที่ดินและสิ่งปลูกสร้าง',
          'signboard': 'ภาษีป้าย',
          'local': 'ภาษีท้องถิ่น'
        };

        return {
          name: taxTypeLabels[type] || type,
          data: monthlyData
        };
      });

      return series;
    }

    // สร้างกราฟเปรียบเทียบยอดชำระภาษี
    var taxCompareOptions = {
      series: processTaxYearlyData(taxYearlyData),
      chart: {
        type: 'bar',
        height: 350,
        stacked: true,
        toolbar: {
          show: true
        }
      },
      plotOptions: {
        bar: {
          horizontal: false,
          columnWidth: '55%',
          endingShape: 'rounded'
        },
      },
      dataLabels: {
        enabled: false
      },
      stroke: {
        show: true,
        width: 2,
        colors: ['transparent']
      },
      xaxis: {
        categories: [
          'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
          'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
        ],
      },
      yaxis: {
        title: {
          text: 'จำนวนเงิน (บาท)'
        },
        labels: {
          formatter: function(value) {
            return new Intl.NumberFormat('th-TH').format(value);
          }
        }
      },
      colors: ['#60A5FA', '#34D399', '#F59E0B'],
      fill: {
        opacity: 1
      },
      tooltip: {
        y: {
          formatter: function(value) {
            return new Intl.NumberFormat('th-TH').format(value) + ' บาท';
          }
        }
      },
      legend: {
        position: 'bottom',
        horizontalAlign: 'center'
      }
    };

    // Process arrears data for chart
    function processArrearsData(data) {
      return data.map(item => ({
        x: {
          'land': 'ภาษีที่ดินและสิ่งปลูกสร้าง',
          'signboard': 'ภาษีป้าย',
          'local': 'ภาษีท้องถิ่น'
        } [item.tax_type] || item.tax_type,
        y: parseInt(item.total_count),
        amount: parseFloat(item.total_amount)
      }));
    }

    // สร้างกราฟแสดงจำนวนผู้ค้างชำระ
    var arrearsOptions = {
      series: [{
        name: 'จำนวนราย',
        data: processArrearsData(arrearsData)
      }],
      chart: {
        type: 'bar',
        height: 350
      },
      plotOptions: {
        bar: {
          horizontal: true,
          borderRadius: 4
        }
      },
      colors: ['#EF4444'],
      dataLabels: {
        enabled: true,
        formatter: function(val) {
          return val + ' ราย';
        }
      },
      xaxis: {
        title: {
          text: 'จำนวนราย'
        }
      },
      tooltip: {
        custom: function({
          series,
          seriesIndex,
          dataPointIndex,
          w
        }) {
          const data = w.config.series[0].data[dataPointIndex];
          return `
        <div class="p-2">
          <div>${data.x}</div>
          <div>จำนวน: ${data.y} ราย</div>
          <div>ยอดค้างชำระ: ${new Intl.NumberFormat('th-TH').format(data.amount)} บาท</div>
        </div>
      `;
        }
      }
    };

    // Render ทุกกราฟ
    new ApexCharts(document.querySelector("#monthlyChart"), monthlyOptions).render();
    new ApexCharts(document.querySelector("#taxTypeChart"), taxTypeOptions).render();
    new ApexCharts(document.querySelector("#statusChart"), statusOptions).render();
    // Render both charts
    new ApexCharts(document.querySelector("#taxCompareChart"), taxCompareOptions).render();
    new ApexCharts(document.querySelector("#arrearsChart"), arrearsOptions).render();

    // ฟังก์ชันช่วยเหลือ
    function getThaiMonth(month) {
      const thaiMonths = [
        'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
        'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
      ];
      return thaiMonths[month - 1];
    }
  });


</script>
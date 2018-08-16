
                /* 
				    GenerateProgressBar：Generate the progressbar for tier information such as GPFS
				    tier:COS,GPFS,etc..
				    barID:The ID of progressbar
				    labelClass:Html sign for class
				 */
                function GenerateProgressBar(tier, tier_size, barID, labelClass) {
                    $.ajax({
                        url: "get_progressbar_values.php",
                        data: {
                            tier: tier,
                            tier_size: tier_size
                        },
                        method: 'POST',
                        success: function(res) {
                            var progressbar = $("#" + barID);
                            progressLabel = $("." + labelClass);
                            var val = res.perc;
                            progressbar.progressbar({
                                value: val,
                            });
                            if (val < 60) {
                                $(".ui-widget-header").css({
                                    'background': 'green'
                                });
                            } else {
                                $(".ui-widget-header").css({
                                    'background': 'yellow'
                                });
                            }
                            var label = res.size;
                            label = String(label);
                            progressLabel.text("已用" + label + "M");
                        }
                    })
                }
                GenerateProgressBar("system", 40960, "progressbar1", "progress-label-1");
                GenerateProgressBar("saspool", 51200, "progressbar2", "progress-label-2");
                GenerateProgressBar("satapool", 61440, "progressbar3", "progress-label-3");
          
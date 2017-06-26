// Locale
d3.timeFormat = d3.timeFormatLocale({
  "dateTime": "%A, le %e %B %Y, %X",
  "date": "%d/%m/%Y",
  "time": "%H:%M:%S",
  "periods": ["AM", "PM"],
  "days": ["dimanche", "lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi"],
  "shortDays": ["dim.", "lun.", "mar.", "mer.", "jeu.", "ven.", "sam."],
  "months": ["janvier", "février", "mars", "avril", "mai", "juin", "juillet", "août", "septembre", "octobre", "novembre", "décembre"],
  "shortMonths": ["Janv.", "Févr.", "Mars", "Avr.", "Mai", "Juin", "Juil.", "Août", "Sept.", "Oct.", "Nov.", "Déc."]
}).format;

// Returns a date corresponding to the week of 'day'.
function get_last_monday(day) {
  day = new Date(day)
  day.setHours(12)  // Trick. Some days are !=24h (daylight savings...)
  var d = new Date(day-24*60*60*1000*(day.getDay()-1));
  d.setHours(0); d.setMinutes(0); d.setSeconds(0);
  return d;
}

function plot_activity_data(url) {
  d3.json(url, function(data) {
    var startdate = get_last_monday(data["date_debut"]);
    var enddate = get_last_monday(new Date());
    var all_weeks = {}; // Becomes a list later
    var idx = 0;
    var mediane = {}; var presence = {}; var participations = {}; var mots = {}; var vacances = {};
    for (var d = new Date(startdate); d <= enddate; d.setDate(d.getDate() + 7)) {
      var md = get_last_monday(d);
      all_weeks[md] = 0;
      presence[md] = data['n_presences']['commission'][idx] + data['n_presences']['hemicycle'][idx];
      participations[md] = data['n_participations']['commission'][idx] + data['n_participations']['hemicycle'][idx];
      mots[md] = data['n_mots']['commission'][idx] + data['n_mots']['hemicycle'][idx];
      mediane[md] = data["presences_medi"]["total"][idx];
      vacances[md] = !!data['vacances'][idx];
      idx++;
    }
    all_weeks = Object.keys(all_weeks)
    
    week_after = get_last_monday(new Date((new Date(all_weeks[all_weeks.length-1])).getTime()+1000*24*60*60*7))
    
    var svg_width=800;
    var svg_height=150;
    $("#presence_chart").css('width',svg_width).css('height',svg_height);
    var margin_left=45;
    var margin_bottom=25;
    var week_width = (svg_width-margin_left)/Object.keys(all_weeks).length+0.2;
    
    // Scales
    timescale = d3.scaleLinear()
      .domain([get_last_monday(startdate), new Date(get_last_monday(enddate).getTime()+1000*60*60*24*7)])
      .range([margin_left,svg_width-2]);
    
    yscale = d3.scaleLinear()
      .domain([0,14])
      .range([svg_height-margin_bottom,4]);
    
    
    // Background horizontal [gray/white] stripes
    grid = d3.select("#presence_chart").append('g').classed('grid',1);
    for(var i=4; i<=yscale.domain()[1]; i=i+4){
      grid.append('rect')
        .style('fill', 'rgb(240,240,240)')
        .attr('width',timescale.range()[1]-timescale.range()[0])
        .attr('height',yscale(0)-yscale(2))
        .attr('y',yscale(i))
        .attr('x',timescale.range()[0]);
    }
    
    plot_area = d3.select("#presence_chart").append('g').classed('plot',1);
    
    // This function is probably useless on real data <------------------------------------------------------------------------------
    // If you remove it, replace 'clip(x,a,b)' by 'x' alone
    function clip(x,a,b) {
      return Math.max(Math.min(x,b),a);
    }
    
    // Presence
    plot_area.append("path").attr("id","curve_presence")
    	  .attr("d", d3.area()
    		     .curve(d3.curveLinear)
    		     .x(function (x){return timescale(new Date(x));})
    		     .y1(function (x){return clip(yscale(presence[x] || 0),yscale(14)+.5,yscale(0)-.5);})
    		     .y0(yscale(0))
    	    (all_weeks.concat([week_after])))
    
    // Participations
    plot_area.append("path").attr("id","curve_participation")
    	  .attr("d", d3.area()
    		     .curve(d3.curveLinear)
    		     .x(function (x){return timescale(new Date(x));})
    		     .y1(function (x){return clip(yscale(participations[x] || 0),yscale(14)+.5,yscale(0)-.5);})
    		     .y0(yscale(0))
    	    (all_weeks.concat([week_after])))
    
    
    // Mediane
    d3.select("#presence_chart").append("path").attr("id","curve_mediane")
      .attr("d", d3.line()
    		.curve(d3.curveLinear)
    		.x(function (x){return timescale(new Date(x));})
    		.y(function (x){return clip(yscale(mediane[x] || 0),yscale(14)+.5,yscale(0)-.5);})
        (all_weeks.concat([week_after])))
    
    plot_area.append("path").attr("id","curve_vacances")
      .attr("d", d3.area()
    		.curve(d3.curveStepAfter)
    		.x(function (x){return timescale(new Date(x));})
    		.y1(function (x){return yscale(14*vacances[x] || 0);})
    		.y0(yscale(0))
        (all_weeks.concat([week_after])))
    
    // Tooltips
    d3.select("#presence_chart").append('g').classed("tooltipRectangle",1).selectAll("rect.tooltip").data(all_weeks).enter()
      .append("rect").classed('tooltip',1)
      .attr('x',function (x){return timescale(new Date(x));})
      .attr('y',yscale(14))
      .attr('width',week_width)
      .attr('height',yscale(0)-yscale(14))
      .attr("date",function (x){return x;})
      .on('mouseover',function (x) {
        $("#tooltip_week").html(d3.timeFormat("%d %b %Y")(new Date(x)));
        $("#tooltip_participations").html(participations[x]);
        $("#tooltip_presences").html(presence[x]);
        $("#tooltip_mediane").html(mediane[x]);
        $("#tooltip_mots").html(mots[x]);
        if(vacances[x]==1){
    	   $("#tooltip #banner_vacances").show();
        }
        else {
    	   $("#tooltip #banner_vacances").hide();
        }
      })
      .on('mousemove', function(e) {
        $('#tooltip').css('left', d3.event.pageX - 200)
             .css('top', d3.event.pageY + 20)
             .show();
      })
      .on('mouseleave', function() {
        $("#tooltip").css("display","none");
      })
    
    // Axes
    d3.select("#presence_chart").append("g").attr('id','yaxis')
      .attr("transform","translate("+(margin_left-6)+",0)")
      .call(d3.axisLeft(yscale).ticks(5))
    d3.select("#presence_chart").append("g").attr('id','timeaxis')
      .attr("transform","translate(0,"+(svg_height-margin_bottom+5)+")")
      .call(d3.axisBottom(timescale).ticks(10).tickFormat(d3.timeFormat("%b %y")))
    
  });
}

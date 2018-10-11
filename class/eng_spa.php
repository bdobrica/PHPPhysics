<?php
class ENG_SPA {
	const SPA_ZA		= 0;
	const SPA_ZA_INC	= 1;
	const SPA_ZA_RTS	= 2;
	const SPA_ALL		= 3;

	private $time;		#

	private $year;		# 4-digit year,    valid range: -2000 to 6000, error code: 1
	private $month;		# 2-digit month,         valid range: 1 to 12, error code: 2
	private $day;		# 2-digit day,           valid range: 1 to 31, error code: 3
	private $hour;		# Observer local hour,   valid range: 0 to 24, error code: 4 
	private $minute;	# Observer local minute, valid range: 0 to 59, error code: 5
	private $second;	# Observer local second, valid range: 0 to 59, error code: 6


	private $delta_ut1;	# Fractional second difference between UTC and UT which is used
				# to adjust UTC for earth's irregular rotation rate and is derived
				# from observation only and is reported in this bulletin:
				# http://maia.usno.navy.mil/ser7/ser7.dat,
				# where delta_ut1 = DUT1
				# valid range: -1 to 1 second (exclusive), error code 17

	private $delta_t;	# Difference between earth rotation time and terrestrial time
				# It is derived from observation only and is reported in this
				# bulletin: http:#maia.usno.navy.mil/ser7/ser7.dat,
				# where delta_t = 32.184 + (TAI-UTC) - DUT1
				# valid range: -8000 to 8000 seconds, error code: 7

	private $timezone;	# Observer time zone (negative west of Greenwich)
				# valid range: -18   to   18 hours,   error code: 8

	private $longitude;	# Observer longitude (negative west of Greenwich)
				# valid range: -180  to  180 degrees, error code: 9

	private $latitude;	# Observer latitude (negative south of equator)
				# valid range: -90   to   90 degrees, error code: 10

	private $elevation;	# Observer elevation [meters]
				# valid range: -6500000 or higher meters,    error code: 11

	private $pressure;	# Annual average local pressure [millibars]
				# valid range:    0 to 5000 millibars,       error code: 12

	private $temperature;	# Annual average local temperature [degrees Celsius]
				# valid range: -273 to 6000 degrees Celsius, error code; 13

	private $slope;		# Surface slope (measured from the horizontal plane)
				# valid range: -360 to 360 degrees, error code: 14

	private $azm_rotation;	# Surface azimuth rotation (measured from south to projection of
				#     surface normal on horizontal plane, negative west)
				# valid range: -360 to 360 degrees, error code: 15

	private $atmos_refract;	# Atmospheric refraction at sunrise and sunset (0.5667 deg is typical)
				# valid range: -5   to   5 degrees, error code: 16

	private $function;	# Switch to choose functions for desired output (from enumeration)

    //-----------------Intermediate OUTPUT VALUES--------------------

	private $jd;          #Julian day
	private $jc;          #Julian century

	private $jde;         #Julian ephemeris day
	private $jce;         #Julian ephemeris century
	private $jme;         #Julian ephemeris millennium

	private $l;           #earth heliocentric longitude [degrees]
	private $b;           #earth heliocentric latitude [degrees]
	private $r;           #earth radius vector [Astronomical Units, AU]

	private $theta;       #geocentric longitude [degrees]
	private $beta;        #geocentric latitude [degrees]

	private $x0;          #mean elongation (moon-sun) [degrees]
	private $x1;          #mean anomaly (sun) [degrees]
	private $x2;          #mean anomaly (moon) [degrees]
	private $x3;          #argument latitude (moon) [degrees]
	private $x4;          #ascending longitude (moon) [degrees]

	private $del_psi;     #nutation longitude [degrees]
	private $del_epsilon; #nutation obliquity [degrees]
	private $epsilon0;    #ecliptic mean obliquity [arc seconds]
	private $epsilon;     #ecliptic true obliquity  [degrees]

	private $del_tau;     #aberration correction [degrees]
	private $lamda;       #apparent sun longitude [degrees]
	private $nu0;         #Greenwich mean sidereal time [degrees]
	private $nu;          #Greenwich sidereal time [degrees]

	private $alpha;       #geocentric sun right ascension [degrees]
	private $delta;       #geocentric sun declination [degrees]

	private $h;           #observer hour angle [degrees]
	private $xi;          #sun equatorial horizontal parallax [degrees]
	private $del_alpha;   #sun right ascension parallax [degrees]
	private $delta_prime; #topocentric sun declination [degrees]
	private $alpha_prime; #topocentric sun right ascension [degrees]
	private $h_prime;     #topocentric local hour angle [degrees]

	private $e0;          #topocentric elevation angle (uncorrected) [degrees]
	private $del_e;       #atmospheric refraction correction [degrees]
	private $e;           #topocentric elevation angle (corrected) [degrees]

	private $eot;         #equation of time [minutes]
	private $srha;        #sunrise hour angle [degrees]
	private $ssha;        #sunset hour angle [degrees]
	private $sta;         #sun transit altitude [degrees]

//---------------------Final OUTPUT VALUES------------------------

	public $zenith;		#topocentric zenith angle [degrees]
	public $azimuth180;	#topocentric azimuth angle (westward from south) [-180 to 180 degrees]
	public $azimuth;	#topocentric azimuth angle (eastward from north) [   0 to 360 degrees]
	public $incidence;	#surface incidence angle [degrees]

	public $suntransit;	#local sun transit time (or solar noon) [fractional hour]
	public $sunrise;	#local sunrise time (+/- 30 seconds) [fractional hour]
	public $sunset;		#local sunset time (+/- 30 seconds) [fractional hour]

//-------------- Utility functions for other applications (such as NREL's SAMPA) --------------
	#double deg2rad(double degrees);
	#double rad2deg(double radians);

	/*
	double limit_degrees(double degrees);
	double third_order_polynomial(double a, double b, double c, double d, double x);
	double geocentric_right_ascension(double lamda, double epsilon, double beta);
	double geocentric_declination(double beta, double epsilon, double lamda);
	double observer_hour_angle(double nu, double longitude, double alpha_deg);
	void   right_ascension_parallax_and_topocentric_dec(double latitude, double elevation,
			 double xi, double h, double delta, double *delta_alpha, double *delta_prime);
	double topocentric_right_ascension(double alpha_deg, double delta_alpha);
	double topocentric_local_hour_angle(double h, double delta_alpha);
	double topocentric_elevation_angle(double latitude, double delta_prime, double h_prime);
	double atmospheric_refraction_correction(double pressure, double temperature,
						     double atmos_refract, double e0);
	double topocentric_elevation_angle_corrected(double e0, double delta_e);
	double topocentric_zenith_angle(double e);
	double topocentric_azimuth_angle_neg180_180(double h_prime, double latitude, double delta_prime);
	double topocentric_azimuth_angle_zero_360(double azimuth180);
	*/


//Calculate SPA output values (in structure) based on input values passed in structure
	/*
	int spa_calculate(spa_data *spa);
	*/

/////////////////////////////////////////////
//      Solar Position Algorithm (SPA)     //
//                   for                   //
//        Solar Radiation Application      //
//                                         //
//               May 12, 2003              //
//                                         //
//   Filename: SPA.C                       //
//                                         //
//   Afshin Michael Andreas                //
//   Afshin.Andreas@NREL.gov (303)384-6383 //
//                                         //
//   Measurement & Instrumentation Team    //
//   Solar Radiation Research Laboratory   //
//   National Renewable Energy Laboratory  //
//   1617 Cole Blvd, Golden, CO 80401      //
/////////////////////////////////////////////

/////////////////////////////////////////////
//   See the SPA.H header file for usage   //
//                                         //
//   This code is based on the NREL        //
//   technical report "Solar Position      //
//   Algorithm for Solar Radiation         //
//   Application" by I. Reda & A. Andreas  //
/////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////////////////////
//
//   NOTICE
//   Copyright (C) 2008-2011 Alliance for Sustainable Energy, LLC, All Rights Reserved
//
//The Solar Position Algorithm ("Software") is code in development prepared by employees of the
//Alliance for Sustainable Energy, LLC, (hereinafter the "Contractor"), under Contract No.
//DE-AC36-08GO28308 ("Contract") with the U.S. Department of Energy (the "DOE"). The United
//States Government has been granted for itself and others acting on its behalf a paid-up, non-
//exclusive, irrevocable, worldwide license in the Software to reproduce, prepare derivative
//works, and perform publicly and display publicly. Beginning five (5) years after the date
//permission to assert copyright is obtained from the DOE, and subject to any subsequent five
//(5) year renewals, the United States Government is granted for itself and others acting on
//its behalf a paid-up, non-exclusive, irrevocable, worldwide license in the Software to
//reproduce, prepare derivative works, distribute copies to the public, perform publicly and
//display publicly, and to permit others to do so. If the Contractor ceases to make this
//computer software available, it may be obtained from DOE's Office of Scientific and Technical
//Information's Energy Science and Technology Software Center (ESTSC) at P.O. Box 1020, Oak
//Ridge, TN 37831-1020. THIS SOFTWARE IS PROVIDED BY THE CONTRACTOR "AS IS" AND ANY EXPRESS OR
//IMPLIED WARRANTIES, INCLUDING BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
//AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE CONTRACTOR OR THE
//U.S. GOVERNMENT BE LIABLE FOR ANY SPECIAL, INDIRECT OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES
//WHATSOEVER, INCLUDING BUT NOT LIMITED TO CLAIMS ASSOCIATED WITH THE LOSS OF DATA OR PROFITS,
//WHICH MAY RESULT FROM AN ACTION IN CONTRACT, NEGLIGENCE OR OTHER TORTIOUS CLAIM THAT ARISES
//OUT OF OR IN CONNECTION WITH THE ACCESS, USE OR PERFORMANCE OF THIS SOFTWARE.
//
//The Software is being provided for internal, noncommercial purposes only and shall not be
//re-distributed. Please contact Anne Miller (Anne.Miller@nrel.gov) in the NREL
//Commercialization and Technology Transfer Office for information concerning a commercial
//license to use the Software.
//
//As a condition of using the Software in an application, the developer of the application
//agrees to reference the use of the Software and make this Notice readily accessible to any
//end-user in a Help|About screen or equivalent manner.
//
///////////////////////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////////////////////
// Revised 27-FEB-2004 Andreas
//         Added bounds check on inputs and return value for spa_calculate().
// Revised 10-MAY-2004 Andreas
//         Changed temperature bound check minimum from -273.15 to -273 degrees C.
// Revised 17-JUN-2004 Andreas
//         Corrected a problem that caused a bogus sunrise/set/transit on the equinox.
// Revised 18-JUN-2004 Andreas
//         Added a "function" input variable that allows the selecting of desired outputs.
// Revised 21-JUN-2004 Andreas
//         Added 3 new intermediate output values to SPA structure (srha, ssha, & sta).
// Revised 23-JUN-2004 Andreas
//         Enumerations for "function" were renamed and 2 were added.
//         Prevented bound checks on inputs that are not used (based on function).
// Revised 01-SEP-2004 Andreas
//         Changed a local variable from integer to double.
// Revised 12-JUL-2005 Andreas
//         Put a limit on the EOT calculation, so that the result is between -20 and 20.
// Revised 26-OCT-2005 Andreas
//         Set the atmos. refraction correction to zero, when sun is below horizon.
//         Made atmos_refract input a requirement for all "functions".
//         Changed atmos_refract bound check from +/- 10 to +/- 5 degrees.
// Revised 07-NOV-2006 Andreas
//         Corrected 3 earth periodic terms in the L_TERMS array.
//         Corrected 2 earth periodic terms in the R_TERMS array.
// Revised 10-NOV-2006 Andreas
//         Corrected a constant used to calculate topocentric sun declination.
//         Put a limit on observer hour angle, so result is between 0 and 360.
// Revised 13-NOV-2006 Andreas
//         Corrected calculation of topocentric sun declination.
//         Converted all floating point inputs in spa structure to doubles.
// Revised 27-FEB-2007 Andreas
//         Minor correction made as to when atmos. refraction correction is set to zero.
// Revised 21-JAN-2008 Andreas
//         Minor change to two variable declarations.
// Revised 12-JAN-2009 Andreas
//         Changed timezone bound check from +/-12 to +/-18 hours.
// Revised 14-JAN-2009 Andreas
//         Corrected a constant used to calculate ecliptic mean obliquity.
// Revised 01-APR-2013 Andreas
//		   Replace floor with new integer function for tech. report consistency, no affect on results.
//         Add "utility" function prototypes to header file for use with NREL's SAMPA.
//         Rename 4 "utility" function names (remove "sun") for clarity with NREL's SAMPA.
//		   Added delta_ut1 as required input, which the fractional second difference between UT and UTC.
//         Time must be input w/o delta_ut1 adjustment, instead of assuming adjustment was pre-applied.
///////////////////////////////////////////////////////////////////////////////////////////////

	const PI		= 3.1415926535897932384626433832795028841971;
	const SUN_RADIUS	= 0.26667;

	const L_COUNT 		= 6;
	const B_COUNT 		= 2;
	const R_COUNT 		= 5;
	const Y_COUNT 		= 63;

	const L_MAX_SUBCOUNT	= 64;
	const B_MAX_SUBCOUNT 	= 5;
	const R_MAX_SUBCOUNT	= 40;

	const TERM_A		= 0;
	const TERM_B		= 1;
	const TERM_C		= 2;
	const TERM_COUNT	= 3;
	const TERM_X0		= 0;
	const TERM_X1		= 1;
	const TERM_X2		= 2;
	const TERM_X3		= 3;
	const TERM_X4		= 4;
	const TERM_X_COUNT	= 5;
	const TERM_Y_COUNT	= 5;
	const TERM_PSI_A	= 0;
	const TERM_PSI_B	= 1;
	const TERM_EPS_C	= 2;
	const TERM_EPS_D	= 3;
	const TERM_PE_COUNT	= 4;
	const JD_MINUS		= 0;
	const JD_ZERO		= 1;
	const JD_PLUS		= 2;
	const JD_COUNT		= 3;
	const SUN_TRANSIT	= 0;
	const SUN_RISE		= 1;
	const SUN_SET		= 2;
	const SUN_COUNT		= 3;
	

	private static $l_subcount = array (64,34,20,7,3,1);
	private static $b_subcount = array (5,2);
	private static $r_subcount = array (40,10,6,2,1);

///////////////////////////////////////////////////
///  Earth Periodic Terms
///////////////////////////////////////////////////
	private static $L_TERMS = array (
		array (
			array (175347046.0,0,0),
			array (3341656.0,4.6692568,6283.07585),
			array (34894.0,4.6261,12566.1517),
			array (3497.0,2.7441,5753.3849),
			array (3418.0,2.8289,3.5231),
			array (3136.0,3.6277,77713.7715),
			array (2676.0,4.4181,7860.4194),
			array (2343.0,6.1352,3930.2097),
			array (1324.0,0.7425,11506.7698),
			array (1273.0,2.0371,529.691),
			array (1199.0,1.1096,1577.3435),
			array (990,5.233,5884.927),
			array (902,2.045,26.298),
			array (857,3.508,398.149),
			array (780,1.179,5223.694),
			array (753,2.533,5507.553),
			array (505,4.583,18849.228),
			array (492,4.205,775.523),
			array (357,2.92,0.067),
			array (317,5.849,11790.629),
			array (284,1.899,796.298),
			array (271,0.315,10977.079),
			array (243,0.345,5486.778),
			array (206,4.806,2544.314),
			array (205,1.869,5573.143),
			array (202,2.458,6069.777),
			array (156,0.833,213.299),
			array (132,3.411,2942.463),
			array (126,1.083,20.775),
			array (115,0.645,0.98),
			array (103,0.636,4694.003),
			array (102,0.976,15720.839),
			array (102,4.267,7.114),
			array (99,6.21,2146.17),
			array (98,0.68,155.42),
			array (86,5.98,161000.69),
			array (85,1.3,6275.96),
			array (85,3.67,71430.7),
			array (80,1.81,17260.15),
			array (79,3.04,12036.46),
			array (75,1.76,5088.63),
			array (74,3.5,3154.69),
			array (74,4.68,801.82),
			array (70,0.83,9437.76),
			array (62,3.98,8827.39),
			array (61,1.82,7084.9),
			array (57,2.78,6286.6),
			array (56,4.39,14143.5),
			array (56,3.47,6279.55),
			array (52,0.19,12139.55),
			array (52,1.33,1748.02),
			array (51,0.28,5856.48),
			array (49,0.49,1194.45),
			array (41,5.37,8429.24),
			array (41,2.4,19651.05),
			array (39,6.17,10447.39),
			array (37,6.04,10213.29),
			array (37,2.57,1059.38),
			array (36,1.71,2352.87),
			array (36,1.78,6812.77),
			array (33,0.59,17789.85),
			array (30,0.44,83996.85),
			array (30,2.74,1349.87),
			array (25,3.16,4690.48)
			),
		array (
			array (628331966747.0,0,0),
			array (206059.0,2.678235,6283.07585),
			array (4303.0,2.6351,12566.1517),
			array (425.0,1.59,3.523),
			array (119.0,5.796,26.298),
			array (109.0,2.966,1577.344),
			array (93,2.59,18849.23),
			array (72,1.14,529.69),
			array (68,1.87,398.15),
			array (67,4.41,5507.55),
			array (59,2.89,5223.69),
			array (56,2.17,155.42),
			array (45,0.4,796.3),
			array (36,0.47,775.52),
			array (29,2.65,7.11),
			array (21,5.34,0.98),
			array (19,1.85,5486.78),
			array (19,4.97,213.3),
			array (17,2.99,6275.96),
			array (16,0.03,2544.31),
			array (16,1.43,2146.17),
			array (15,1.21,10977.08),
			array (12,2.83,1748.02),
			array (12,3.26,5088.63),
			array (12,5.27,1194.45),
			array (12,2.08,4694),
			array (11,0.77,553.57),
			array (10,1.3,6286.6),
			array (10,4.24,1349.87),
			array (9,2.7,242.73),
			array (9,5.64,951.72),
			array (8,5.3,2352.87),
			array (6,2.65,9437.76),
			array (6,4.67,4690.48)
			),
		array (
			array (52919.0,0,0),
			array (8720.0,1.0721,6283.0758),
			array (309.0,0.867,12566.152),
			array (27,0.05,3.52),
			array (16,5.19,26.3),
			array (16,3.68,155.42),
			array (10,0.76,18849.23),
			array (9,2.06,77713.77),
			array (7,0.83,775.52),
			array (5,4.66,1577.34),
			array (4,1.03,7.11),
			array (4,3.44,5573.14),
			array (3,5.14,796.3),
			array (3,6.05,5507.55),
			array (3,1.19,242.73),
			array (3,6.12,529.69),
			array (3,0.31,398.15),
			array (3,2.28,553.57),
			array (2,4.38,5223.69),
			array (2,3.75,0.98)
    			),
    		array (
			array (289.0,5.844,6283.076),
			array (35,0,0),
			array (17,5.49,12566.15),
			array (3,5.2,155.42),
			array (1,4.72,3.52),
			array (1,5.3,18849.23),
			array (1,5.97,242.73)
			),
		array (
			array (114.0,3.142,0),
			array (8,4.13,6283.08),
			array (1,3.84,12566.15)
			),
		array (
        		array (1,3.14,0)
			)
		);

	private static $B_TERMS = array (
		array (
			array (280.0,3.199,84334.662),
			array (102.0,5.422,5507.553),
			array (80,3.88,5223.69),
			array (44,3.7,2352.87),
			array (32,4,1577.34)
			),
		array (
			array (9,3.9,5507.55),
			array (6,1.73,5223.69)
			)
		);

	private static $R_TERMS = array (
		array (
			array (100013989.0,0,0),
			array (1670700.0,3.0984635,6283.07585),
			array (13956.0,3.05525,12566.1517),
			array (3084.0,5.1985,77713.7715),
			array (1628.0,1.1739,5753.3849),
			array (1576.0,2.8469,7860.4194),
			array (925.0,5.453,11506.77),
			array (542.0,4.564,3930.21),
			array (472.0,3.661,5884.927),
			array (346.0,0.964,5507.553),
			array (329.0,5.9,5223.694),
			array (307.0,0.299,5573.143),
			array (243.0,4.273,11790.629),
			array (212.0,5.847,1577.344),
			array (186.0,5.022,10977.079),
			array (175.0,3.012,18849.228),
			array (110.0,5.055,5486.778),
			array (98,0.89,6069.78),
			array (86,5.69,15720.84),
			array (86,1.27,161000.69),
			array (65,0.27,17260.15),
			array (63,0.92,529.69),
			array (57,2.01,83996.85),
			array (56,5.24,71430.7),
			array (49,3.25,2544.31),
			array (47,2.58,775.52),
			array (45,5.54,9437.76),
			array (43,6.01,6275.96),
			array (39,5.36,4694),
			array (38,2.39,8827.39),
			array (37,0.83,19651.05),
			array (37,4.9,12139.55),
			array (36,1.67,12036.46),
			array (35,1.84,2942.46),
			array (33,0.24,7084.9),
			array (32,0.18,5088.63),
			array (32,1.78,398.15),
			array (28,1.21,6286.6),
			array (28,1.9,6279.55),
			array (26,4.59,10447.39)
			),
		array (
			array (103019.0,1.10749,6283.07585),
			array (1721.0,1.0644,12566.1517),
			array (702.0,3.142,0),
			array (32,1.02,18849.23),
			array (31,2.84,5507.55),
			array (25,1.32,5223.69),
			array (18,1.42,1577.34),
			array (10,5.91,10977.08),
			array (9,1.42,6275.96),
			array (9,0.27,5486.78)
			),
		array (
			array (4359.0,5.7846,6283.0758),
			array (124.0,5.579,12566.152),
			array (12,3.14,0),
			array (9,3.63,77713.77),
			array (6,1.87,5573.14),
			array (3,5.47,18849.23)
			),
		array (
			array (145.0,4.273,6283.076),
			array (7,3.92,12566.15)
			),
		array (
			array (4,2.56,6283.08)
			)
		);

////////////////////////////////////////////////////////////////
///  Periodic Terms for the nutation in longitude and obliquity
////////////////////////////////////////////////////////////////

	private static $Y_TERMS = array (
		array (0,0,0,0,1),
		array (-2,0,0,2,2),
		array (0,0,0,2,2),
		array (0,0,0,0,2),
		array (0,1,0,0,0),
		array (0,0,1,0,0),
		array (-2,1,0,2,2),
		array (0,0,0,2,1),
		array (0,0,1,2,2),
		array (-2,-1,0,2,2),
		array (-2,0,1,0,0),
		array (-2,0,0,2,1),
		array (0,0,-1,2,2),
		array (2,0,0,0,0),
		array (0,0,1,0,1),
		array (2,0,-1,2,2),
		array (0,0,-1,0,1),
		array (0,0,1,2,1),
		array (-2,0,2,0,0),
		array (0,0,-2,2,1),
		array (2,0,0,2,2),
		array (0,0,2,2,2),
		array (0,0,2,0,0),
		array (-2,0,1,2,2),
		array (0,0,0,2,0),
		array (-2,0,0,2,0),
		array (0,0,-1,2,1),
		array (0,2,0,0,0),
		array (2,0,-1,0,1),
		array (-2,2,0,2,2),
		array (0,1,0,0,1),
		array (-2,0,1,0,1),
		array (0,-1,0,0,1),
		array (0,0,2,-2,0),
		array (2,0,-1,2,1),
		array (2,0,1,2,2),
		array (0,1,0,2,2),
		array (-2,1,1,0,0),
		array (0,-1,0,2,2),
		array (2,0,0,2,1),
		array (2,0,1,0,0),
		array (-2,0,2,2,2),
		array (-2,0,1,2,1),
		array (2,0,-2,0,1),
		array (2,0,0,0,1),
		array (0,-1,1,0,0),
		array (-2,-1,0,2,1),
		array (-2,0,0,0,1),
		array (0,0,2,2,1),
		array (-2,0,2,0,1),
		array (-2,1,0,2,1),
		array (0,0,1,-2,0),
		array (-1,0,1,0,0),
		array (-2,1,0,0,0),
		array (1,0,0,0,0),
		array (0,0,1,2,0),
		array (0,0,-2,2,2),
		array (-1,-1,1,0,0),
		array (0,1,1,0,0),
		array (0,-1,1,2,2),
		array (2,-1,-1,2,2),
		array (0,0,3,2,2),
		array (2,-1,0,2,2),
		);

	private static $PE_TERMS = array (
		array (-171996,-174.2,92025,8.9),
		array (-13187,-1.6,5736,-3.1),
		array (-2274,-0.2,977,-0.5),
		array (2062,0.2,-895,0.5),
		array (1426,-3.4,54,-0.1),
		array (712,0.1,-7,0),
		array (-517,1.2,224,-0.6),
		array (-386,-0.4,200,0),
		array (-301,0,129,-0.1),
		array (217,-0.5,-95,0.3),
		array (-158,0,0,0),
		array (129,0.1,-70,0),
		array (123,0,-53,0),
		array (63,0,0,0),
		array (63,0.1,-33,0),
		array (-59,0,26,0),
		array (-58,-0.1,32,0),
		array (-51,0,27,0),
		array (48,0,0,0),
		array (46,0,-24,0),
		array (-38,0,16,0),
		array (-31,0,13,0),
		array (29,0,0,0),
		array (29,0,-12,0),
		array (26,0,0,0),
		array (-22,0,0,0),
		array (21,0,-10,0),
		array (17,-0.1,0,0),
		array (16,0,-8,0),
		array (-16,0.1,7,0),
		array (-15,0,9,0),
		array (-13,0,7,0),
		array (-12,0,6,0),
		array (11,0,0,0),
		array (-10,0,5,0),
		array (-8,0,3,0),
		array (7,0,-3,0),
		array (-7,0,0,0),
		array (-7,0,3,0),
		array (-7,0,3,0),
		array (6,0,0,0),
		array (6,0,-3,0),
		array (6,0,-3,0),
		array (-6,0,3,0),
		array (-6,0,3,0),
		array (5,0,0,0),
		array (-5,0,3,0),
		array (-5,0,3,0),
		array (-5,0,3,0),
		array (4,0,0,0),
		array (4,0,0,0),
		array (4,0,0,0),
		array (-4,0,0,0),
		array (-4,0,0,0),
		array (-4,0,0,0),
		array (3,0,0,0),
		array (-3,0,0,0),
		array (-3,0,0,0),
		array (-3,0,0,0),
		array (-3,0,0,0),
		array (-3,0,0,0),
		array (-3,0,0,0),
		array (-3,0,0,0),
		);

///////////////////////////////////////////////

	private static function limit_degrees ($degrees) {
    		$degrees /= 360.0;
		$limited = 360.0*($degrees-floor($degrees));
		if ($limited < 0) $limited += 360.0;
		return $limited;
		}

	private static function limit_degrees180pm ($degrees) {
    		$degrees /= 360.0;
		$limited = 360.0*($degrees-floor($degrees));
		if      ($limited < -180.0) $limited += 360.0;
		else if ($limited >  180.0) $limited -= 360.0;

		return $limited;
		}

	private static function limit_degrees180 ($degrees) {
		$degrees /= 180.0;
		$limited = 180.0*($degrees-floor($degrees));
		if ($limited < 0) $limited += 180.0;

		return $limited;
		}

	private static function limit_zero2one ($value) {
		$limited = $value - floor($value);
		if ($limited < 0) $limited += 1.0;
		return $limited;
		}

	private static function limit_minutes ($minutes) {
		$limited = $minutes;
		if      ($limited < -20.0) $limited += 1440.0;
		else if ($limited >  20.0) $limited -= 1440.0;

		return $limited;
		}

	private static function dayfrac_to_local_hr ($dayfrac, 	$timezone) {
		return 24.0*self::limit_zero2one($dayfrac + $timezone/24.0);
		}

	private static function third_order_polynomial ($a, $b, $c, $d, $x) {
		return (($a*$x + $b)*$x + $c)*$x + $d;
		}

///////////////////////////////////////////////////////////////////////////////////////////////
	public function __construct ($time = null, $athmosphere = null, $timezone = null, $position = null) {
		$this->time = is_null ($time) ? time () : (int) $time;

		$this->year	= (int) date ('Y', $this->time);
		$this->month	= (int) date ('n', $this->time);
		$this->day	= (int) date ('j', $this->time);
		$this->hour	= (int) date ('G', $this->time);
		$this->minute	= (int) date ('i', $this->time);
		$this->second	= (int) date ('s', $this->time);

		$this->pressure		= (float) (isset ($athmosphere['pressure']) ? $athmosphere['pressure'] : 820);
		$this->temperature 	= (float) (isset ($athmosphere['temperature']) ? $athmosphere['temperature'] : 11);
		$this->atmos_refract 	= (float) (isset ($athmosphere['refraction']) ? $athmosphere['refraction'] : 0.5667);

		$this->delta_ut1	= 0;	# [-1,1]
		$this->delta_t		= 67;	# [-8000,8000];
		$this->timezone		= -7.0;	# [-18,18];

		$this->longitude	= (float) (isset ($position['longitude']) ? $position['longitude'] : 0)		# [-180,180];
		$this->latitude		= (float) (isset ($position['latitude']) ? $position['latitude'] : 0);		# [-90,90];
		$this->elevation	= (float) (isset ($position['elevation']) ? $position['elevation'] : 0);	# [-6500000,];
		
		$this->slope		= (float) (isset ($position['slope']) ? $position['slope'] : 0); 		# [-360.00, 360.00];
		$this->azm_rotation	= (float) (isset ($position['rotation']) ? $position['rotation'] : 0);		# [-360.00, 360.00]; , mesured from south, negative west
		}

///////////////////////////////////////////////////////////////////////////////////////////////
	private function julian_day () {
		$day_decimal = $this->day + ($this->hour - $this->timezone + ($this->minute + ($this->second + $this->delta_ut1)/60.0)/60.0)/24.0;

		if ($this->month < 3) {
			$this->month += 12;
			$this->year--;
			}

		$this->jd =  (int) (365.25*($this->year+4716.0)) + (int) (30.6001*($this->month+1)) + $day_decimal - 1524.5;

		if ($this->jd > 2299160.0) {
			$a = (int) ($this->year/100);
			$this->jd += (2 - $a + (int) ($a/4));
			}
		}

	private function julian_century () {
		$this->jc = ($this->jd-2451545.0)/36525.0;
		}

	private function julian_ephemeris_day () {
		$this->jde = $this->jd + $this->delta_t/86400.0;
		}

	private function julian_ephemeris_century () {
		$this->jce = ($this->jde - 2451545.0)/36525.0;
		}

	private function julian_ephemeris_millennium (){
		$this->jme = ($this->jce/10.0);
		}

	private function earth_periodic_term_summation ($terms) {
		$sum = 0.0;
		for ($i = 0; $i < count ($terms); $i++)
			$sum += $terms[$i][self::TERM_A]*cos($terms[$i][self::TERM_B]+$terms[$i][self::TERM_C]*$this->jme);

		return $sum;
		}

	private function earth_values($term_sum) {
		$sum = 0.0;

		for ($i = 0; $i < count($term_sum); $i++)
			$sum += $term_sum[$i]*pow($this->jme, $i);
		$sum /= 1.0e8;

		return $sum;
		}

	private function earth_heliocentric_longitude () {
		$sum = array ();
		for ($i = 0; $i < self::L_COUNT; $i++)
			$sum[$i] = $this->earth_periodic_term_summation(self::$L_TERMS[$i]);

		$this->l = self::limit_degrees(rad2deg($this->earth_values($sum)));
		}

	private function earth_heliocentric_latitude () {
		$sum = array ();
		for ($i = 0; $i < self::B_COUNT; $i++)
			$sum[$i] = $this->earth_periodic_term_summation(self::$B_TERMS[$i]);

		$this->b = rad2deg($this->earth_values($sum));
		}

	private function earth_radius_vector () {
		$sum = array ();

		for ($i = 0; $i < self::R_COUNT; $i++)
			$sum[$i] = $this->earth_periodic_term_summation(self::$R_TERMS[$i]);

		$this->r = $this->earth_values($sum);
		}

	private function geocentric_longitude () {
		$this->theta = $this->l + 180.0;
		if ($this->theta >= 360.0) $this->theta -= 360.0;
		}

	private function geocentric_latitude () {
		$this->beta = -$this->b;
		}

	private function mean_elongation_moon_sun () {
		return $this->x0 = self::third_order_polynomial (1.0/189474.0, -0.0019142, 445267.11148, 297.85036, $this->jce);
		}

	private function mean_anomaly_sun () {
		return $this->x1 = self::third_order_polynomial (-1.0/300000.0, -0.0001603, 35999.05034, 357.52772, $this->jce);
		}

	private function mean_anomaly_moon () {
		return $this->x2 = self::third_order_polynomial (1.0/56250.0, 0.0086972, 477198.867398, 134.96298, $this->jce);
		}

	private function argument_latitude_moon () {
		return $this->x3 = self::third_order_polynomial (1.0/327270.0, -0.0036825, 483202.017538, 93.27191, $this->jce);
		}

	private function ascending_longitude_moon () {
		return $this->x4 = self::third_order_polynomial (1.0/450000.0, 0.0020708, -1934.136261, 125.04452, $this->jce);
		}

	private function xy_term_summation ($i, $x) {
		$sum = 0.0;

		for ($j = 0; $j < self::TERM_Y_COUNT; $j++)
			$sum += $x[$j]*self::$Y_TERMS[$i][$j];

		return $sum;
		}

	private function nutation_longitude_and_obliquity ($x) {
		$sum_psi = 0.0;
		$sum_epsilon = 0.0;
		for ($i = 0; $i < self::Y_COUNT; $i++) {
			$xy_term_sum  = deg2rad(self::xy_term_summation($i, $x));
			$sum_psi += (self::$PE_TERMS[$i][self::TERM_PSI_A] + $this->jce*self::$PE_TERMS[$i][self::TERM_PSI_B])*sin($xy_term_sum);
			$sum_epsilon += (self::$PE_TERMS[$i][self::TERM_EPS_C] + $this->jce*self::$PE_TERMS[$i][self::TERM_EPS_D])*cos($xy_term_sum);
			}

		$this->del_psi = $sum_psi / 36000000.0;
		$this->del_epsilon = $sum_epsilon / 36000000.0;
		}

	private function ecliptic_mean_obliquity () {
		$u = $this->jme/10.0;
		$this->epsilon0 = 84381.448 + $u*(-4680.93 + $u*(-1.55 + $u*(1999.25 + $u*(-51.38 + $u*(-249.67 +
				   $u*(  -39.05 + $u*( 7.12 + $u*(  27.87 + $u*(  5.79 + $u*2.45)))))))));
		}

	private function ecliptic_true_obliquity () {
		$this->epsilon = $this->del_epsilon + $this->epsilon0/3600.0;
		}

	private function aberration_correction () {
		$this->del_tau = -20.4898 / (3600.0*$this->r);
		}

	private function apparent_sun_longitude () {
		$this->lamda = $this->theta + $this->del_psi + $this->del_tau;
		}

	private function greenwich_mean_sidereal_time () {
		$this->nu0 = self::limit_degrees(280.46061837 + 360.98564736629 * ($this->jd - 2451545.0) +
					       $this->jc*$this->jc*(0.000387933 - $this->jc/38710000.0));
		}

	private function greenwich_sidereal_time () {
		$this->nu = $this->nu0 + $this->del_psi*cos(deg2rad($this->epsilon));
		}

	private function geocentric_right_ascension () {
		$lamda_rad   = deg2rad($this->lamda);
		$epsilon_rad = deg2rad($this->epsilon);
		$this->alpha = self::limit_degrees(rad2deg(atan2(sin($lamda_rad)*cos($epsilon_rad) -
					       tan(deg2rad($this->beta))*sin($epsilon_rad), cos($lamda_rad))));
		}

	private function geocentric_declination () {
		$beta_rad    = deg2rad($this->beta);
		$epsilon_rad = deg2rad($this->epsilon);

		$this->delta = rad2deg(asin(sin($beta_rad)*cos($epsilon_rad) +
				cos($beta_rad)*sin($epsilon_rad)*sin(deg2rad($this->lamda))));
		}

	private function observer_hour_angle () {
		$this->h = self::limit_degrees($this->nu + $this->longitude - $this->alpha);
		}

	private function sun_equatorial_horizontal_parallax () {
		$this->xi = 8.794 / (3600.0 * $this->r);
		}

	private function right_ascension_parallax_and_topocentric_dec () {
		$lat_rad = deg2rad($this->latitude);
		$xi_rad = deg2rad($this->xi);
		$h_rad = deg2rad($this->h);
		$delta_rad = deg2rad($this->delta);

		$u = atan(0.99664719 * tan($lat_rad));
		$y = 0.99664719 * sin($u) + $this->elevation*sin($lat_rad)/6378140.0;
		$x =              cos($u) + $this->elevation*cos($lat_rad)/6378140.0;

		$delta_alpha_rad = atan2(	- $x*sin($xi_rad) * sin($h_rad),
				cos($delta_rad) - $x*sin($xi_rad) * cos($h_rad));

		$this->delta_prime = rad2deg(atan2((sin($delta_rad) - $y*sin($xi_rad))*cos($delta_alpha_rad),
						    cos($delta_rad) - $x*sin($xi_rad) *cos($h_rad)));

		$this->del_alpha = rad2deg($delta_alpha_rad);
		}

	private function topocentric_right_ascension () {
		$this->alpha_prime = $this->alpha + $this->del_alpha;
		}

	private function topocentric_local_hour_angle () {
		$this->h_prime = $this->h - $this->del_alpha;
		}

	private function topocentric_elevation_angle () {
		$lat_rad         = deg2rad($this->latitude);
		$delta_prime_rad = deg2rad($this->delta_prime);

		$this->e0 = rad2deg(asin(sin($lat_rad)*sin($delta_prime_rad) +
				    cos($lat_rad)*cos($delta_prime_rad) * cos(deg2rad($this->h_prime))));
		}

	private function atmospheric_refraction_correction () {
		$this->del_e = 0.0;

		if ($this->e0 >= -1*(self::SUN_RADIUS + $this->atmos_refract))
			$this->del_e = ($this->pressure / 1010.0) * (283.0 / (273.0 + $this->temperature)) *
				1.02 / (60.0 * tan(deg2rad($this->e0 + 10.3/($this->e0 + 5.11))));
		}

	private function topocentric_elevation_angle_corrected () {
		$this->e = $this->e0 + $this->del_e;
		}

	private function topocentric_zenith_angle () {
		$this->zenith = 90.0 - $this->e;
		}

	private function topocentric_azimuth_angle_neg180_180 () {
		$h_prime_rad = deg2rad($this->h_prime);
		$lat_rad     = deg2rad($this->latitude);

		$this->azimuth180 = rad2deg(atan2(sin($h_prime_rad),
			 cos($h_prime_rad)*sin($lat_rad) - tan(deg2rad($this->delta_prime))*cos($lat_rad)));
		}

	private function topocentric_azimuth_angle_zero_360 () {
		$this->azimuth = $this->azimuth180 + 180.0;
		}

	private function surface_incidence_angle () {
		$zenith_rad = deg2rad($this->zenith);
		$slope_rad  = deg2rad($this->slope);

		$this->incidence = rad2deg(acos(cos($zenith_rad)*cos($slope_rad)  +
			sin($slope_rad )*sin($zenith_rad) * cos(deg2rad($this->azimuth180 - $this->azm_rotation))));
		}

	private function sun_mean_longitude () {
		return self::limit_degrees(280.4664567 + $this->jme*(360007.6982779 + $this->jme*(0.03032028 +
			$this->jme*(1/49931.0   + $this->jme*(-1/15300.0     + $this->jme*(-1/2000000.0))))));
		}

	private function eot ($m) {
		$this->eot = self::limit_minutes(4.0*($m - 0.0057183 - $this->alpha + $this->del_psi*cos(deg2rad($this->epsilon))));
		}

	private function approx_sun_transit_time ($alpha_zero, $nu) {
		return ($alpha_zero - $this->longitude - $nu) / 360.0;
		}

	private function sun_hour_angle_at_rise_set($delta_zero, $h0_prime) {
		$h0             = -99999;
		$latitude_rad   = deg2rad($this->latitude);
		$delta_zero_rad = deg2rad($delta_zero);
		$argument       = (sin(deg2rad($h0_prime)) - sin($latitude_rad)*sin($delta_zero_rad)) /
							    (cos($latitude_rad)*cos($delta_zero_rad));

		if (abs($argument) <= 1) $h0 = self::limit_degrees180(rad2deg(acos($argument)));

		return $h0;
		}

	private function approx_sun_rise_and_set ($m_rts, $h0) {
		$h0_dfrac = $h0/360.0;
		$m_rts[self::SUN_RISE]    = self::limit_zero2one($m_rts[self::SUN_TRANSIT] - $h0_dfrac);
		$m_rts[self::SUN_SET]     = self::limit_zero2one($m_rts[self::SUN_TRANSIT] + $h0_dfrac);
		$m_rts[self::SUN_TRANSIT] = self::limit_zero2one($m_rts[self::SUN_TRANSIT]);
		return $m_rts;
		}

	private function rts_alpha_delta_prime($ad, $n) {
		$a = $ad[self::JD_ZERO] - $ad[self::JD_MINUS];
		$b = $ad[self::JD_PLUS] - $ad[self::JD_ZERO];

		if (abs($a) >= 2.0) $a = self::limit_zero2one($a);
		if (abs($b) >= 2.0) $b = self::limit_zero2one($b);

		return $ad[self::JD_ZERO] + $n * ($a + $b + ($b-$a)*$n)/2.0;
		}

	private function rts_sun_altitude ($delta_prime, $h_prime) {
		$latitude_rad    = deg2rad($this->latitude);
		$delta_prime_rad = deg2rad($delta_prime);

		return rad2deg(asin(sin($latitude_rad)*sin($delta_prime_rad) +
				    cos($latitude_rad)*cos($delta_prime_rad)*cos(deg2rad($h_prime))));
		}

	private function sun_rise_and_set ($m_rts, $h_rts, $delta_prime, $h_prime, $h0_prime, $sun) {
		return $m_rts[$sun] + ($h_rts[$sun] - $h0_prime) /
		  (360.0*cos(deg2rad($delta_prime[$sun]))*cos(deg2rad($this->latitude))*sin(deg2rad($h_prime[$sun])));
		}

////////////////////////////////////////////////////////////////////////////////////////////////
// Calculate required SPA parameters to get the right ascension (alpha) and declination (delta)
// Note: JD must be already calculated and in structure
////////////////////////////////////////////////////////////////////////////////////////////////
	private function calculate_geocentric_sun_right_ascension_and_declination () {
		$this->julian_century ();
		$this->julian_ephemeris_day ();
		$this->julian_ephemeris_century ();
		$this->julian_ephemeris_millennium ();

		$this->earth_heliocentric_longitude ();
		$this->earth_heliocentric_latitude ();
		$this->earth_radius_vector ();

		$this->geocentric_longitude ();
		$this->geocentric_latitude ();

		$x = array (
			$this->mean_elongation_moon_sun (),
			$this->mean_anomaly_sun (),
			$this->mean_anomaly_moon (),
			$this->argument_latitude_moon (),
			$this->ascending_longitude_moon ());

		$this->nutation_longitude_and_obliquity ($x);
		$this->ecliptic_mean_obliquity ();
		$this->ecliptic_true_obliquity ();

		$this->aberration_correction ();
		$this->apparent_sun_longitude ();
		$this->greenwich_mean_sidereal_time  ();
		$this->greenwich_sidereal_time  ();

		$this->geocentric_right_ascension ();
		$this->geocentric_declination ();
		}

////////////////////////////////////////////////////////////////////////
// Calculate Equation of Time (EOT) and Sun Rise, Transit, & Set (RTS)
////////////////////////////////////////////////////////////////////////

	private function calculate_eot_and_sun_rise_transit_set () {
		$alpha = array ();
		$delta = array ();
		$m_rts = array ();
		$nu_rts = array ();
		$h_rts = array ();
		$alpha_prime = array ();
		$delta_prime = array ();
		$h_prime = array ();

		$h0_prime = -1*(self::SUN_RADIUS + $this->atmos_refract);

		$sun_rts = clone $this;
		$m = $this->sun_mean_longitude ();
		$this->eot ($m);

		$sun_rts->hour = $sun_rts->minute = $sun_rts->second = 0;
		$sun_rts->delta_ut1 = $sun_rts->timezone = 0.0;

		$sun_rts->julian_day ();
		$sun_rts->calculate_geocentric_sun_right_ascension_and_declination ();
		$nu = $sun_rts->nu;

		$sun_rts->delta_t = 0;
		$sun_rts->jd--;

		for ($i = 0; $i < self::JD_COUNT; $i++) {
			$sun_rts->calculate_geocentric_sun_right_ascension_and_declination ();
			$alpha[$i] = $sun_rts->alpha;
			$delta[$i] = $sun_rts->delta;
			$sun_rts->jd++;
			}

		$m_rts[self::SUN_TRANSIT] = $this->approx_sun_transit_time($alpha[self::JD_ZERO], $nu);
		$h0 = $this->sun_hour_angle_at_rise_set($delta[self::JD_ZERO], $h0_prime);

		if ($h0 >= 0) {
			$m_rts = $this->approx_sun_rise_and_set($m_rts, $h0);

			for ($i = 0; $i < self::SUN_COUNT; $i++) {
				$nu_rts[$i]      = $nu + 360.985647*$m_rts[$i];
				$n               = $m_rts[$i] + $this->delta_t/86400.0;
				$alpha_prime[$i] = $this->rts_alpha_delta_prime($alpha, $n);
				$delta_prime[$i] = $this->rts_alpha_delta_prime($delta, $n);

				$h_prime[$i]     = self::limit_degrees180pm($nu_rts[$i] + $this->longitude - $alpha_prime[$i]);
				$h_rts[$i]       = $this->rts_sun_altitude($delta_prime[$i], $h_prime[$i]);
				}

			print_r ($m_rts);

			$this->srha = $h_prime[self::SUN_RISE];
			$this->ssha = $h_prime[self::SUN_SET];
			$this->sta  = $h_rts[self::SUN_TRANSIT];

			$this->suntransit = self::dayfrac_to_local_hr($m_rts[self::SUN_TRANSIT] - $h_prime[self::SUN_TRANSIT] / 360.0,
							      $this->timezone);

			$this->sunrise = self::dayfrac_to_local_hr($this->sun_rise_and_set($m_rts, $h_rts, $delta_prime,
					$h_prime, $h0_prime, self::SUN_RISE), $this->timezone);

			$this->sunset  = self::dayfrac_to_local_hr($this->sun_rise_and_set($m_rts, $h_rts, $delta_prime,
					$h_prime, $h0_prime, self::SUN_SET),  $this->timezone);

			}
		else
			$this->srha = $this->ssha = $this->sta = $this->suntransit = $this->sunrise = $this->sunset = -99999;

		}

///////////////////////////////////////////////////////////////////////////////////////////
// Calculate all SPA parameters and put into structure
// Note: All inputs values (listed in header file) must already be in structure
///////////////////////////////////////////////////////////////////////////////////////////
	public function test () {
		printf("Julian Day:    %.6f\n",$this->jd);
		printf("E:             %.6f\n",$this->e);
		printf("E0:            %.6f\n",$this->e0);
		printf("HPrime:        %.6f\n",$this->h_prime);
		printf("L:             %.6e degrees\n",$this->l);
		printf("B:             %.6e degrees\n",$this->b);
		printf("R:             %.6f AU\n",$this->r);
		printf("H:             %.6f degrees\n",$this->h);
		printf("Delta Alpha:   %.6e degrees\n",$this->del_alpha);
		printf("Delta Prime:   %.6e degrees\n",$this->delta_prime);
		printf("Delta Psi:     %.6e degrees\n",$this->del_psi);
		printf("Delta Epsilon: %.6e degrees\n",$this->del_epsilon);
		printf("Epsilon:       %.6f degrees\n",$this->epsilon);
		printf("Zenith:        %.6f degrees\n",$this->zenith);
		printf("Azimuth:       %.6f degrees\n",$this->azimuth);
		printf("Incidence:     %.6f degrees\n",$this->incidence);

		$min = 60.0*($this->sunrise - (int)($this->sunrise));
		$sec = 60.0*($min - (int)$min);
		printf("Sunrise:       %02d:%02d:%02d Local Time\n", (int)($this->sunrise), (int)$min, (int)$sec);

		$min = 60.0*($this->sunset - (int)($this->sunset));
		$sec = 60.0*($min - (int)$min);
		printf("Sunset:        %02d:%02d:%02d Local Time\n", (int)($this->sunset), (int)$min, (int)$sec);
		}

	public function calculate ($function = null) {
		$function = is_null ($function) ? self::SPA_ALL : $function;

		$this->julian_day ();
		$this->calculate_geocentric_sun_right_ascension_and_declination ();

		$this->observer_hour_angle ();
		$this->sun_equatorial_horizontal_parallax ();

		$this->right_ascension_parallax_and_topocentric_dec ();

		$this->topocentric_right_ascension ();
		$this->topocentric_local_hour_angle ();

		$this->topocentric_elevation_angle ();
		$this->atmospheric_refraction_correction ();
		$this->topocentric_elevation_angle_corrected ();

		$this->topocentric_zenith_angle ();
		$this->topocentric_azimuth_angle_neg180_180 ();
		$this->topocentric_azimuth_angle_zero_360 ();

		if (($function == self::SPA_ZA_INC) || ($function == self::SPA_ALL))
			$this->surface_incidence_angle ();

		if (($function == self::SPA_ZA_RTS) || ($function == self::SPA_ALL))
			$this->calculate_eot_and_sun_rise_transit_set ();

		}
///////////////////////////////////////////////////////////////////////////////////////////
	}

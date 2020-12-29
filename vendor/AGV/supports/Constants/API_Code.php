<?php

namespace BREND\Constants;

class API_Code{
    const ALL               = 1;
    const BATTERY           = 2;
    const POS               = 3;
    const IsLeftUp          = 4;
    const PreviewPath       = 100;
    const PathScript        = 101;

    const DIRECTSTOP        = -1;
    const SCRIPTOVER        = -2;
    const SCRIPTContinue    = -3;
    const SCRIPTSTOP        = -4;
    const ServoOn           = -5;
    const ServoOff          = -6;
    const RUN1000           = -10;
    const TURNLEFT          = -11;
    const TURNRIGHT         = -12;
    const SHELFUP           = -13;
    const SHELFDOWN         = -14;
    const TURNBACK          = -15;
    const PLUGIN            = -16;
    const PLUGOUT           = -17;
    const TURNREG           = -20;
    const DoScript          = -1000;
    const GoChargeing       = -1001;
}
import React from 'react';
import {shallow} from 'enzyme';
import FindByPostcode from "../../assets/js/pages/FindByPostcode";

it("renders without crashing", () => {
    shallow(<FindByPostcode />);
});

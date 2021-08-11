import React from 'react';
import {shallow} from 'enzyme';
import About from "../../assets/js/pages/About";

it("renders without crashing", () => {
    shallow(<About />);
});

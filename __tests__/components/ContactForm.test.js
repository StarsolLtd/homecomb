import React from 'react';
import {shallow} from 'enzyme';
import ContactForm from "../../assets/js/components/ContactForm";

it("renders without crashing", () => {
    shallow(<ContactForm />);
});

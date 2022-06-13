import React from 'react'
import { mount, shallow } from 'enzyme'
import Scale from '../../assets/js/components/Scale'
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";

it('renders and shows the low meaning and high meaning in correct classes', () => {
  const wrapper = shallow(<Scale lowMeaning={'Bad'} highMeaning={'Great'} />)
  expect(wrapper.find('span.low-meaning').text().includes('Bad')).toBe(true)
  expect(wrapper.find('span.high-meaning').text().includes('Great')).toBe(true)
})

it('clicking a rating changes Scale state', () => {
  const wrapper = mount(<Scale rating={2} />)
  expect(wrapper.state('rating')).toEqual(2)
  const fourthStar = wrapper.find(FontAwesomeIcon).at(3)
  fourthStar.simulate('click')
  // TODO why is this setting rating to 1 and not 4?
  expect(wrapper.state('rating')).toEqual(4)
})

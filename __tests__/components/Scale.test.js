import React from 'react'
import { shallow } from 'enzyme'
import Scale from '../../assets/js/components/Scale'

it('renders and shows the low meaning and high meaning in correct classes', () => {
  const wrapper = shallow(<Scale lowMeaning={'Bad'} highMeaning={'Great'} />)
  expect(wrapper.find('span.low-meaning').text().includes('Bad')).toBe(true)
  expect(wrapper.find('span.high-meaning').text().includes('Great')).toBe(true)
})
